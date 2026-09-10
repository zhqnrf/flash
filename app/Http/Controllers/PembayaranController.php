<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registrasi;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    // Halaman Pembayaran per Event: detail pelatihan + daftar peserta
    public function index(Event $event)
    {
        $event->load('pelatihan');

        $pesertas = Registrasi::where('event_id', $event->id)
            ->orderByRaw("FIELD(status_pendaftaran, 'Menunggu','Diterima','Ditolak')")
            ->latest()
            ->get();

        return view('event.pembayaran', compact('event', 'pesertas'));
    }

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

        // Cicil: peserta tetap DITERIMA (boleh ikut pelatihan), pembayaran berstatus Cicil
        $kekurangan = min((float) $request->kekurangan, $biaya);

        $registrasi->update([
            'status_pendaftaran' => 'Diterima',
            'status_pembayaran' => $kekurangan <= 0 ? 'Lunas' : 'Cicil',
            'total_dibayar' => max($biaya - $kekurangan, 0),
        ]);

        return back()->with('success', 'Peserta diterima dengan status Cicil. Link pelunasan sudah aktif, silakan kirim ke peserta.');
    }

    // Tolak pendaftaran peserta
    public function tolak(Registrasi $registrasi)
    {
        $registrasi->update(['status_pendaftaran' => 'Ditolak']);

        return back()->with('success', 'Pendaftaran peserta telah ditolak.');
    }

    // Admin update manual sisa kekurangan (peserta nyicil bertahap / nambah bayar)
    public function updateCicilan(Request $request, Registrasi $registrasi)
    {
        $request->validate([
            'kekurangan' => 'required|numeric|min:0',
        ]);

        $biaya = (float) $registrasi->event->biaya_pelatihan;
        $kekurangan = min((float) $request->kekurangan, $biaya);

        $registrasi->update([
            'total_dibayar' => max($biaya - $kekurangan, 0),
            'status_pembayaran' => $kekurangan <= 0 ? 'Lunas' : 'Cicil',
        ]);

        return back()->with('success', 'Data cicilan peserta berhasil diperbarui.');
    }

    // Tandai pembayaran lunas (setelah admin verifikasi bukti bayar terakhir)
    public function tandaiLunas(Registrasi $registrasi)
    {
        $registrasi->update([
            'status_pembayaran' => 'Lunas',
            'total_dibayar' => $registrasi->event->biaya_pelatihan,
        ]);

        return back()->with('success', 'Pembayaran peserta ditandai LUNAS. Link pelunasan otomatis nonaktif.');
    }
}