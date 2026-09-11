<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registrasi;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
class AbsensiController extends Controller
{
    // ================================================================
    // HALAMAN PUBLIK: Peserta cari nama & absen selfie
    // ================================================================
    public function create($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        $pesertas = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->with('absensi')
            ->orderBy('nama')
            ->get(['id', 'nama', 'gelar_depan', 'gelar_belakang', 'instansi']);

        $bukaAbsensi = $this->cekWaktuAbsensi($event);

        return view('public.absensi.form', compact('event', 'pesertas', 'bukaAbsensi'));
    }

    public function store(Request $request, $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        if (!$this->cekWaktuAbsensi($event)) {
            return back()->with('error', 'Waktu absensi sudah ditutup atau belum dibuka. Silakan hubungi panitia.');
        }

        $request->validate([
            'registrasi_id' => 'required|exists:registrasis,id',
            'foto_selfie' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'registrasi_id.required' => 'Silakan cari dan pilih nama Anda terlebih dahulu.',
            'foto_selfie.required' => 'Foto selfie wajib diambil sebelum submit.',
        ]);

        $registrasi = Registrasi::where('id', $request->registrasi_id)
            ->where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->firstOrFail();

        if ($registrasi->absensi) {
            return back()->with('error', 'Anda sudah melakukan absensi sebelumnya pada pukul ' . $registrasi->absensi->jam_masuk->format('H:i') . ' WIB.');
        }

        $path = $request->file('foto_selfie')->store('absensi/selfie', 'public');

        Absensi::create([
            'registrasi_id' => $registrasi->id,
            'foto_selfie' => $path,
            'jam_masuk' => now(),
        ]);

        return back()->with('success', 'Selamat, ' . $registrasi->nama_lengkap . '! Absensi Anda berhasil dicatat pukul ' . now()->format('H:i') . ' WIB.');
    }

    private function cekWaktuAbsensi(Event $event)
    {
        // 1. Jika Buka Paksa
        if ($event->status_absen === 'buka_paksa') return true;

        // 2. Jika Tutup Paksa
        if ($event->status_absen === 'tutup_paksa') return false;

        // 3. Jika Otomatis (Cek Jam)
        $sekarang = Carbon::now('Asia/Jakarta');
        $jamSekarang = $sekarang->format('H:i:s');
        $hariIni = $sekarang->format('Y-m-d');

        if ($hariIni < $event->tanggal_mulai || $hariIni > $event->tanggal_selesai) {
            return false;
        }

        $mulai = Carbon::parse($event->waktu_presensi_mulai)->format('H:i:s');
        $selesai = Carbon::parse($event->waktu_presensi_selesai)->format('H:i:s');

        return $jamSekarang >= $mulai && $jamSekarang <= $selesai;
    }

    // ==========================================
    // UPDATE STATUS PORTAL ABSENSI
    // ==========================================
    public function updateStatus(Request $request, Event $event)
    {
        $request->validate([
            'status_absen' => 'required|in:otomatis,buka_paksa,tutup_paksa'
        ]);

        $event->update(['status_absen' => $request->status_absen]);
        
        $pesan = [
            'otomatis' => 'dikembalikan ke mode otomatis (sesuai jam).',
            'buka_paksa' => 'DIBUKA PAKSA. Peserta bisa absen kapan saja.',
            'tutup_paksa' => 'DITUTUP PAKSA. Peserta tidak bisa absen.'
        ];

        return back()->with('success', 'Status portal absensi ' . $pesan[$request->status_absen]);
    }
    // ==========================================
    // FITUR BARU: TUTUP PAKSA ABSENSI (ADMIN)
    // ==========================================
    public function toggleAbsen(Event $event)
    {
        // Balikkan nilai boolean (jika true jadi false, jika false jadi true)
        $event->update(['is_absen_tutup' => !$event->is_absen_tutup]);
        
        $status = $event->is_absen_tutup ? 'ditutup paksa' : 'dibuka kembali (mengikuti jam otomatis)';
        return back()->with('success', "Akses portal absensi berhasil {$status}.");
    }

    // ==========================================
    // FITUR BARU: RESET ABSENSI PESERTA (ADMIN)
    // ==========================================
    public function resetAbsensi(Absensi $absensi)
    {
        // Hapus file foto selfie dari storage (jika ada)
        if ($absensi->foto_selfie && Storage::disk('public')->exists($absensi->foto_selfie)) {
            Storage::disk('public')->delete($absensi->foto_selfie);
        }
        
        // Hapus data absensi dari database
        $absensi->delete();

        return back()->with('success', 'Data absensi peserta berhasil direset/dihapus.');
    }

    public function rekap(Request $request, Event $event)
    {
        $event->load('pelatihan');

        // Query Utama: Ambil peserta yang "Diterima"
        $query = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->with('absensi');

        // 1. Filter Pencarian (Nama, Instansi, NIK, Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email_plataran_sehat', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // 2. Filter Status Kehadiran
        if ($request->filled('status') && $request->status !== 'Semua') {
            if ($request->status === 'Hadir') {
                $query->whereHas('absensi');
            } else if ($request->status === 'Belum') {
                $query->whereDoesntHave('absensi');
            }
        }

        // 3. Urutkan (Waktu Daftar atau Abjad)
        if ($request->input('sort') === 'asc') {
            $query->oldest('nama'); // Urut A-Z
        } else {
            $query->latest(); // Terbaru
        }

        // Pagination
        $pesertas = $query->paginate(15)->withQueryString();

        // Data utuh semua peserta (tanpa paginasi) untuk Export SheetJS
        $semuaDataExport = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->with('absensi')
            ->get();

        // Kalkulasi Dashboard
        $totalDiterima = $semuaDataExport->count();
        $totalHadir = $semuaDataExport->filter(fn($p) => $p->absensi)->count();
        $totalBelumHadir = $totalDiterima - $totalHadir;
        $persenHadir = $totalDiterima > 0 ? round(($totalHadir / $totalDiterima) * 100) : 0;

        // Cek status absensi saat ini (Buka/Tutup sesuai jam di database)
        $bukaAbsensi = $this->cekWaktuAbsensi($event);

        return view('event.absensi', compact(
            'event', 'pesertas', 'totalDiterima', 'totalHadir', 
            'totalBelumHadir', 'persenHadir', 'bukaAbsensi', 'semuaDataExport'
        ));
    }
}