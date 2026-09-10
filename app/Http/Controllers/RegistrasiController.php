<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registrasi;
use Illuminate\Http\Request;

class RegistrasiController extends Controller
{
    // Halaman Pendaftaran (Publik)
    public function create($uuid)
    {
        // Cari event berdasarkan UUID yang ada di link
        $event = Event::where('uuid', $uuid)->firstOrFail();

        return view('public.registrasi.form', compact('event'));
    }

    public function store(Request $request, $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'nama' => 'required|string|max:255',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'nik' => 'required|numeric|digits:16',
            'email_plataran_sehat' => 'required|email',
            'instansi' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string',
            'nip' => 'nullable|string|max:50',
            'pangkat_golongan' => 'nullable|string|max:50',
            'departemen' => 'nullable|string|max:255',
            'ukuran_kaos' => 'nullable|string|max:10',
            'bukti_bayar_pertama' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'komitmen' => 'required|in:Ya'
        ], [
            'komitmen.in' => 'Anda wajib menyetujui komitmen peserta untuk melanjutkan pendaftaran.',
            'bukti_bayar_pertama.image' => 'Bukti bayar harus berupa gambar (JPG/PNG).'
        ]);

        // Cek NIK ganda di event yang sama
        $cekNik = Registrasi::where('event_id', $event->id)->where('nik', $request->nik)->first();
        if ($cekNik) {
            return back()->withInput()->with('error', 'NIK ini sudah terdaftar pada event ini!');
        }

        // Upload Bukti Bayar Pertama jika ada
        $pathBukti = null;
        if ($request->hasFile('bukti_bayar_pertama')) {
            $pathBukti = $request->file('bukti_bayar_pertama')->store('registrasi/bukti_bayar', 'public');
        }

        Registrasi::create([
            'event_id' => $event->id,
            'nama' => $request->nama,
            'gelar_depan' => $request->gelar_depan,
            'gelar_belakang' => $request->gelar_belakang,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nik' => $request->nik,
            'email_plataran_sehat' => $request->email_plataran_sehat,
            'nip' => $request->nip,
            'pangkat_golongan' => $request->pangkat_golongan,
            'instansi' => $request->instansi,
            'departemen' => $request->departemen,
            'alamat_lengkap' => $request->alamat_lengkap,
            'ukuran_kaos' => $request->ukuran_kaos,
            'bukti_bayar_pertama' => $pathBukti,
            'komitmen' => true
        ]);

        return redirect()->back()->with('success', 'Pendaftaran berhasil dikirim! Silakan menunggu verifikasi dari Admin.');
    }

    // ================================================================
    // HALAMAN PUBLIK: Pelunasan Cicilan
    // Link ini dikirim admin ke peserta yang status pembayarannya "Cicil"
    // ================================================================
    public function showPembayaran($uuid)
    {
        $registrasi = Registrasi::where('uuid', $uuid)->firstOrFail();

        // Kalau sudah lunas, link otomatis nggak berlaku lagi
        if ($registrasi->status_pembayaran === 'Lunas') {
            abort(404);
        }

        return view('public.registrasi.pembayaran', compact('registrasi'));
    }

    public function storePembayaran(Request $request, $uuid)
    {
        $registrasi = Registrasi::where('uuid', $uuid)->firstOrFail();

        if ($registrasi->status_pembayaran === 'Lunas') {
            abort(404);
        }

        $request->validate([
            'bukti_bayar_terakhir' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'bukti_bayar_terakhir.image' => 'Bukti bayar harus berupa gambar (JPG/PNG).'
        ]);

        $path = $request->file('bukti_bayar_terakhir')->store('registrasi/bukti_bayar', 'public');

        $registrasi->update(['bukti_bayar_terakhir' => $path]);

        return back()->with('success', 'Bukti pembayaran berhasil dikirim. Admin akan segera memverifikasi.');
    }
}