<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    // Halaman Pembayaran per Event: detail pelatihan + daftar peserta + Dashboard Statistik
    public function index(Request $request, Event $event)
    {
        $event->load('pelatihan');
        $biaya = (float) $event->biaya_pelatihan;

        // --- 1. QUERY UNTUK LIST TABEL UTAMA (DENGAN FILTER & SEARCH) ---
        $query = Registrasi::where('event_id', $event->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email_plataran_sehat', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status_pembayaran', $request->status);
        }

        if ($request->input('sort') === 'asc') {
            $query->oldest();
        } else {
            $query->latest(); // default desc
        }

        $pesertas = $query->paginate(15)->withQueryString();
        $semuaDataExport = $query->get();

        // --- 2. KALKULASI DASHBOARD REKAP (Dari SEMUA data di event ini) ---
        $allPeserta = Registrasi::where('event_id', $event->id)->get();

        // Total Seharusnya (Potensi Pendapatan = Peserta Aktif x Biaya)
        $pesertaAktifCount = $allPeserta->where('status_pendaftaran', '!=', 'Ditolak')->count();
        $totalSeharusnya = $pesertaAktifCount * $biaya;

        // Total Uang Masuk Keseluruhan
        $totalUangMasuk = $allPeserta->sum('total_dibayar');

        // Rekap Lunas
        $pesertaLunas = $allPeserta->where('status_pembayaran', 'Lunas');
        $totalUangLunas = $pesertaLunas->sum('total_dibayar');
        $countLunas = $pesertaLunas->count();

        // Rekap Piutang / Kurang (Hanya yang berstatus Diterima tapi Belum Lunas)
        $pesertaKurang = $allPeserta->filter(function($p) {
            return $p->status_pendaftaran === 'Diterima' && in_array($p->status_pembayaran, ['Cicil', 'Belum Bayar']);
        });
        $totalUangKurang = $pesertaKurang->reduce(function($carry, $p) use ($biaya) {
            return $carry + max($biaya - $p->total_dibayar, 0);
        }, 0);
        $countCicil = $pesertaKurang->count();

        return view('event.pembayaran', compact(
            'event', 'pesertas', 'totalUangMasuk', 'totalUangLunas', 
            'totalUangKurang', 'pesertaKurang', 'semuaDataExport',
            'totalSeharusnya', 'pesertaAktifCount', 'countLunas', 'countCicil'
        ));
    }

    // ACC pendaftaran -> admin pilih Lunas atau Cicil
    public function acc(Request $request, Registrasi $registrasi)
    {
        $request->validate([
            'keputusan' => 'required|in:Lunas,Cicil',
            'kekurangan' => 'required_if:keputusan,Cicil|nullable|numeric|min:0',
        ], [
            'kekurangan.required_if' => 'Jumlah kekurangan wajib diisi untuk status Cicil.',
        ]);

        $biaya = (float) $registrasi->event->biaya_pelatihan;

        if ($request->keputusan === 'Lunas') {
            $registrasi->update([
                'status_pendaftaran' => 'Diterima',
                'status_pembayaran' => 'Lunas',
                'total_dibayar' => $biaya,
            ]);

            return back()->with('success', 'Peserta diterima dan pembayaran dinyatakan lunas.');
        }

        $kekurangan = min((float) $request->kekurangan, $biaya);
        $registrasi->update([
            'status_pendaftaran' => 'Diterima',
            'status_pembayaran' => $kekurangan <= 0 ? 'Lunas' : 'Cicil',
            'total_dibayar' => max($biaya - $kekurangan, 0),
        ]);

        return back()->with('success', 'Peserta diterima dengan status Cicil. Link pelunasan sudah aktif, silakan kirim ke peserta.');
    }

    public function tolak(Registrasi $registrasi)
    {
        $registrasi->update(['status_pendaftaran' => 'Ditolak']);
        return back()->with('success', 'Pendaftaran peserta telah ditolak.');
    }

    public function updateCicilan(Request $request, Registrasi $registrasi)
    {
        $request->validate(['kekurangan' => 'required|numeric|min:0']);

        $biaya = (float) $registrasi->event->biaya_pelatihan;
        $kekurangan = min((float) $request->kekurangan, $biaya);

        $registrasi->update([
            'total_dibayar' => max($biaya - $kekurangan, 0),
            'status_pembayaran' => $kekurangan <= 0 ? 'Lunas' : 'Cicil',
        ]);

        return back()->with('success', 'Data cicilan peserta berhasil diperbarui.');
    }

    public function tandaiLunas(Registrasi $registrasi)
    {
        $registrasi->update([
            'status_pembayaran' => 'Lunas',
            'total_dibayar' => $registrasi->event->biaya_pelatihan,
        ]);

        return back()->with('success', 'Pembayaran peserta ditandai LUNAS. Link pelunasan otomatis nonaktif.');
    }

    // ==========================================
    // BARU: Hapus data peserta (registrasi) sepenuhnya
    // ==========================================
    public function hapusPeserta(Registrasi $registrasi)
    {
        // Menghapus file gambar bukti bayar dari storage
        if ($registrasi->bukti_bayar_pertama) {
            Storage::disk('public')->delete($registrasi->bukti_bayar_pertama);
        }
        if ($registrasi->bukti_bayar_terakhir) {
            Storage::disk('public')->delete($registrasi->bukti_bayar_terakhir);
        }

        $nama = $registrasi->nama_lengkap ?? $registrasi->nama;
        // Absensi & jawaban evaluasi terkait ikut terhapus otomatis (bila memakai relasi cascade di database)
        $registrasi->delete();

        return back()->with('success', "Data peserta {$nama} berhasil dihapus.");
    }

    // ==========================================
    // BARU: Edit cepat data peserta (dari modal di halaman pembayaran)
    // ==========================================
    public function updateQuickEdit(Request $request, Registrasi $registrasi)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'departemen' => 'nullable|string|max:255',
            'email_plataran_sehat' => 'required|email',
            'no_whatsapp' => 'required|string|min:9|max:15',
        ]);

        // Standarisasi nomor WA (Ubah awalan 0 menjadi 62)
        $noWa = preg_replace('/[^0-9]/', '', $request->no_whatsapp);
        if (substr($noWa, 0, 1) === '0') {
            $noWa = '62' . substr($noWa, 1);
        } elseif (substr($noWa, 0, 2) !== '62') {
            $noWa = '62' . $noWa;
        }

        $registrasi->update([
            'nama' => $request->nama,
            'instansi' => $request->instansi,
            'departemen' => $request->departemen,
            'email_plataran_sehat' => $request->email_plataran_sehat,
            'no_whatsapp' => $noWa,
        ]);

        return back()->with('success', 'Data peserta berhasil diperbarui.');
    }
}