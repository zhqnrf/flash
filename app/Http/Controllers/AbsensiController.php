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
    // HALAMAN PUBLIK: Peserta cari nama & absen selfie (per hari)
    // ================================================================
    public function create($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        $tanggalMulai = Carbon::parse($event->tanggal_mulai)->startOfDay();
        $tanggalSelesai = Carbon::parse($event->tanggal_selesai)->startOfDay();
        $hariIni = Carbon::now()->startOfDay();

        $totalHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        $hariKe = $hariIni->between($tanggalMulai, $tanggalSelesai)
            ? ($tanggalMulai->diffInDays($hariIni) + 1)
            : null;

        $pesertas = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->with(['absensis' => function($q) {
                $q->whereDate('tanggal', now()->toDateString());
            }])
            ->orderBy('nama')
            ->get(['id', 'nama', 'gelar_depan', 'gelar_belakang', 'instansi'])
            ->map(function($p) {
                $absen = $p->absensis->first();
                $jam = null;
                if ($absen && $absen->jam_masuk) {
                    $jam = Carbon::parse($absen->jam_masuk)->format('H:i');
                }

                return [
                    'id' => $p->id,
                    'nama' => $p->nama_lengkap,
                    'instansi' => $p->instansi,
                    'sudah_absen' => $p->absensis->isNotEmpty(),
                    'jam' => $jam,
                ];
            });

        $bukaAbsensi = $this->cekWaktuAbsensiHariIni($event);

        return view('public.absensi.form', compact('event', 'pesertas', 'bukaAbsensi', 'hariKe', 'totalHari'));
    }

    public function store(Request $request, $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        if (!$this->cekWaktuAbsensiHariIni($event)) {
            return back()->with('error', 'Waktu absensi hari ini ditutup. Silakan hubungi panitia.');
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

        $sudahHariIni = $registrasi->absensis()->whereDate('tanggal', now()->toDateString())->first();

        if ($sudahHariIni) {
            return back()->with('error', 'Anda sudah absen hari ini pada pukul ' . Carbon::parse($sudahHariIni->jam_masuk)->format('H:i') . ' WIB.');
        }

        $path = $request->file('foto_selfie')->store('absensi/selfie', 'public');

        Absensi::create([
            'registrasi_id' => $registrasi->id,
            'tanggal' => now()->toDateString(),
            'foto_selfie' => $path,
            'jam_masuk' => now(),
        ]);

        return back()->with('success', 'Selamat, ' . $registrasi->nama_lengkap . '! Absensi hari ini tercatat pukul ' . now()->format('H:i') . ' WIB.');
    }

    private function cekWaktuAbsensiHariIni(Event $event)
    {
        if ($event->status_absen === 'buka_paksa') return true;
        if ($event->status_absen === 'tutup_paksa') return false;

        $hariIni = Carbon::now()->startOfDay();
        $tanggalMulai = Carbon::parse($event->tanggal_mulai)->startOfDay();
        $tanggalSelesai = Carbon::parse($event->tanggal_selesai)->startOfDay();

        if (!$hariIni->between($tanggalMulai, $tanggalSelesai)) return false;

        $mulai = Carbon::parse(now()->toDateString() . ' ' . $event->waktu_presensi_mulai);
        $selesai = Carbon::parse(now()->toDateString() . ' ' . $event->waktu_presensi_selesai);

        return Carbon::now()->between($mulai, $selesai);
    }

    public function updateStatus(Request $request, Event $event)
    {
        $request->validate(['status_absen' => 'required|in:otomatis,buka_paksa,tutup_paksa']);
        $event->update(['status_absen' => $request->status_absen]);
        
        $pesan = [
            'otomatis' => 'dikembalikan ke mode otomatis.',
            'buka_paksa' => 'DIBUKA PAKSA.',
            'tutup_paksa' => 'DITUTUP PAKSA.'
        ];

        return back()->with('success', 'Status portal absensi ' . $pesan[$request->status_absen]);
    }

    public function resetAbsensi(Absensi $absensi)
    {
        if ($absensi->foto_selfie && Storage::disk('public')->exists($absensi->foto_selfie)) {
            Storage::disk('public')->delete($absensi->foto_selfie);
        }
        $absensi->delete();
        return back()->with('success', 'Data absensi peserta pada tanggal tersebut berhasil direset.');
    }

    public function rekap(Request $request, Event $event)
    {
        $event->load('pelatihan');
        $tanggalMulai = Carbon::parse($event->tanggal_mulai)->startOfDay();
        $tanggalSelesai = Carbon::parse($event->tanggal_selesai)->startOfDay();
        $totalHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        
        $daftarTanggal = collect(range(0, $totalHari - 1))->map(function($i) use ($tanggalMulai) {
            return $tanggalMulai->copy()->addDays($i);
        });

        $query = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->with('absensis');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            if ($request->status === 'Lengkap') {
                $query->has('absensis', '=', $totalHari);
            } else if ($request->status === 'Sebagian') {
                $query->has('absensis', '>', 0)->has('absensis', '<', $totalHari);
            } else if ($request->status === 'Tidak') {
                $query->doesntHave('absensis');
            }
        }

        if ($request->input('sort') === 'asc') {
            $query->oldest('nama');
        } else {
            $query->latest();
        }

        $pesertas = $query->paginate(15)->withQueryString();

        $pesertas->getCollection()->transform(function ($p) use ($daftarTanggal) {
            $p->rekap_harian = $daftarTanggal->map(function ($tgl) use ($p) {
                $absen = $p->absensis->first(function($a) use ($tgl) {
                    return Carbon::parse($a->tanggal)->isSameDay($tgl);
                });
                
                $jam = null;
                if ($absen && $absen->jam_masuk) {
                    $jam = Carbon::parse($absen->jam_masuk)->format('H:i');
                }

                return [
                    'hadir' => (bool) $absen,
                    'id_absen' => $absen ? $absen->id : null,
                    'jam' => $jam,
                    'foto' => $absen ? $absen->foto_selfie : null,
                ];
            });
            $p->total_hadir = $p->rekap_harian->where('hadir', true)->count();
            return $p;
        });

        $semuaDataExport = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->with('absensis')
            ->get()
            ->map(function ($p) use ($daftarTanggal) {
                $p->rekap_harian = $daftarTanggal->map(function ($tgl) use ($p) {
                    $absen = $p->absensis->first(function($a) use ($tgl) {
                        return Carbon::parse($a->tanggal)->isSameDay($tgl);
                    });
                    
                    $jam = 'Tidak Hadir';
                    if ($absen && $absen->jam_masuk) {
                        $jam = Carbon::parse($absen->jam_masuk)->format('H:i WIB');
                    }

                    return [
                        'tanggal_label' => $tgl->translatedFormat('d M Y'),
                        'hadir' => (bool) $absen,
                        'jam' => $jam,
                    ];
                });
                $p->total_hadir = $p->rekap_harian->where('hadir', true)->count();
                return $p;
            });

        $totalDiterima = $semuaDataExport->count();
        $totalHadirLengkap = $semuaDataExport->where('total_hadir', $totalHari)->count();
        $totalHadirSebagian = $semuaDataExport->filter(function($p) use ($totalHari) {
            return $p->total_hadir > 0 && $p->total_hadir < $totalHari;
        })->count();
        $totalTidakHadir = $semuaDataExport->where('total_hadir', 0)->count();
        
        $bukaAbsensi = $this->cekWaktuAbsensiHariIni($event);

        return view('event.absensi', compact(
            'event', 'pesertas', 'totalDiterima', 'totalHadirLengkap', 
            'totalHadirSebagian', 'totalTidakHadir', 'totalHari', 
            'daftarTanggal', 'bukaAbsensi', 'semuaDataExport'
        ));
    }
}