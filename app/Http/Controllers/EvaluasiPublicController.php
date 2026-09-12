<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registrasi;
use App\Models\Fasilitator;
use App\Models\EvaluasiPelatihan;
use App\Models\EvaluasiFasilitator;
use App\Models\EvaluasiPelatihanJawaban;
use App\Models\EvaluasiFasilitatorJawaban;
use Illuminate\Http\Request;

class EvaluasiPublicController extends Controller
{
    // ================================================================
    // HALAMAN UTAMA: Evaluasi Pelatihan + Daftar Pemateri
    // ================================================================
    public function create($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $event->load('pelatihan');

        $sudahIsiIds = EvaluasiPelatihanJawaban::where('event_id', $event->id)
            ->pluck('registrasi_id')->unique();

        $pesertas = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->orderBy('nama')
            ->get(['id', 'nama', 'gelar_depan', 'gelar_belakang', 'instansi'])
            ->map(fn($p) => [
                'id' => $p->id,
                'nama' => $p->nama_lengkap,
                'instansi' => $p->instansi,
                'sudah_isi' => $sudahIsiIds->contains($p->id),
            ]);

        // Kriteria evaluasi pelatihan: spesifik pelatihan ini + kriteria global
        $kriteria = EvaluasiPelatihan::where(function ($q) use ($event) {
            $q->where('pelatihan_id', $event->pelatihan_id)->orWhereNull('pelatihan_id');
        })->orderBy('id')->get();

        // Daftar pemateri unik untuk event ini
        $pemateri = $event->eventFasilitatorMateris()
            ->with(['fasilitator', 'evaluasiMateri'])
            ->get()
            ->filter(fn($efm) => $efm->fasilitator)
            ->groupBy('fasilitator_id')
            ->map(function ($items) {
                $first = $items->first();
                return [
                    'id' => $first->fasilitator_id,
                    'nama' => $first->fasilitator->nama_fasilitator,
                    'materi' => $items->pluck('evaluasiMateri.nama_materi')->filter()->unique()->values()->implode(', '),
                ];
            })->values();

        // Map: registrasi_id => [fasilitator_id yang sudah dievaluasi]
        $evaluatedMap = EvaluasiFasilitatorJawaban::where('event_id', $event->id)
            ->get()
            ->groupBy('registrasi_id')
            ->map(fn($items) => $items->pluck('fasilitator_id')->unique()->values());

        $initialRegistrasiId = request()->query('registrasi');

        return view('public.evaluasi.pelatihan', compact('event', 'pesertas', 'kriteria', 'pemateri', 'evaluatedMap', 'initialRegistrasiId'));
    }

    public function store(Request $request, $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'registrasi_id' => 'required|exists:registrasis,id',
            'nilai' => 'required|array|min:1',
            'nilai.*' => 'required|integer|min:1',
            'saran' => 'nullable|string|max:2000',
        ], [
            'registrasi_id.required' => 'Silakan cari dan pilih nama Anda terlebih dahulu.',
            'nilai.required' => 'Semua poin evaluasi wajib diisi.',
        ]);

        $registrasi = Registrasi::where('id', $request->registrasi_id)
            ->where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->firstOrFail();

        $sudahIsi = EvaluasiPelatihanJawaban::where('registrasi_id', $registrasi->id)
            ->where('event_id', $event->id)->exists();

        if ($sudahIsi) {
            return back()->with('error', 'Anda sudah mengisi evaluasi pelatihan ini sebelumnya. Terima kasih!');
        }

        foreach ($request->nilai as $evaluasiPelatihanId => $nilai) {
            EvaluasiPelatihanJawaban::create([
                'registrasi_id' => $registrasi->id,
                'event_id' => $event->id,
                'evaluasi_pelatihan_id' => $evaluasiPelatihanId,
                'nilai' => $nilai,
                'saran' => $request->saran,
            ]);
        }

        return back()->with('success', 'Terima kasih, ' . $registrasi->nama_lengkap . '! Evaluasi pelatihan Anda berhasil disimpan.');
    }

    // ================================================================
    // HALAMAN EVALUASI KHUSUS 1 FASILITATOR
    // ================================================================
    public function createFasilitator($uuid, $fasilitatorId)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $fasilitator = Fasilitator::findOrFail($fasilitatorId);

        $pesertas = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->orderBy('nama')
            ->get(['id', 'nama', 'gelar_depan', 'gelar_belakang', 'instansi'])
            ->map(fn($p) => [
                'id' => $p->id,
                'nama' => $p->nama_lengkap,
                'instansi' => $p->instansi,
            ]);

        $kriteria = EvaluasiFasilitator::where(function ($q) use ($event) {
            $q->where('pelatihan_id', $event->pelatihan_id)->orWhereNull('pelatihan_id');
        })->orderBy('id')->get();

        // Materi yang diajarkan fasilitator ini pada event ini, HANYA yang sudah "waktunya"
        // (tanggal_sesi kosong dianggap selalu tersedia, untuk kompatibilitas data lama)
        $hariIni = now()->toDateString();
        $materiTersedia = $event->eventFasilitatorMateris()
            ->where('fasilitator_id', $fasilitator->id)
            ->with('evaluasiMateri')
            ->get()
            ->filter(fn($efm) => $efm->evaluasiMateri && (is_null($efm->tanggal_sesi) || $efm->tanggal_sesi->toDateString() <= $hariIni))
            ->unique('evaluasi_materi_id')
            ->map(fn($efm) => [
                'id' => $efm->evaluasi_materi_id,
                'nama' => $efm->evaluasiMateri->nama_materi,
                'tanggal_sesi' => optional($efm->tanggal_sesi)->translatedFormat('d M Y'),
            ])->values();

        $totalMateriKeseluruhan = $event->eventFasilitatorMateris()
            ->where('fasilitator_id', $fasilitator->id)
            ->distinct('evaluasi_materi_id')
            ->count('evaluasi_materi_id');

        // Map: registrasi_id => [evaluasi_materi_id yang SUDAH dinilai peserta itu, utk fasilitator ini]
        $sudahDinilaiMap = EvaluasiFasilitatorJawaban::where('event_id', $event->id)
            ->where('fasilitator_id', $fasilitator->id)
            ->get()
            ->groupBy('registrasi_id')
            ->map(fn($rows) => $rows->pluck('evaluasi_materi_id')->unique()->values());

        $initialRegistrasiId = request()->query('registrasi');

        return view('public.evaluasi.fasilitator', compact(
            'event', 'fasilitator', 'pesertas', 'kriteria', 'materiTersedia', 'totalMateriKeseluruhan', 'sudahDinilaiMap', 'initialRegistrasiId'
        ));
    }

    public function storeFasilitator(Request $request, $uuid, $fasilitatorId)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $fasilitator = Fasilitator::findOrFail($fasilitatorId);

        $request->validate([
            'registrasi_id' => 'required|exists:registrasis,id',
            'nilai' => 'required|array|min:1',
            'saran' => 'nullable|string|max:2000',
        ], [
            'registrasi_id.required' => 'Silakan cari dan pilih nama Anda terlebih dahulu.',
            'nilai.required' => 'Semua poin evaluasi wajib diisi.',
        ]);

        $registrasi = Registrasi::where('id', $request->registrasi_id)
            ->where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->firstOrFail();

        $hariIni = now()->toDateString();

        // Pastikan materi yang dikirim memang valid: milik fasilitator ini & sudah waktunya (anti-akal-akalan lewat request manual)
        $materiValidIds = $event->eventFasilitatorMateris()
            ->where('fasilitator_id', $fasilitator->id)
            ->with('evaluasiMateri')
            ->get()
            ->filter(fn($efm) => is_null($efm->tanggal_sesi) || $efm->tanggal_sesi->toDateString() <= $hariIni)
            ->pluck('evaluasi_materi_id')
            ->unique();

        // Materi yang sudah dinilai sebelumnya oleh peserta ini (jangan dinilai dobel)
        $sudahDinilaiIds = EvaluasiFasilitatorJawaban::where('registrasi_id', $registrasi->id)
            ->where('fasilitator_id', $fasilitator->id)
            ->where('event_id', $event->id)
            ->pluck('evaluasi_materi_id')->unique();

        $adaTersimpan = false;

        foreach ($request->nilai as $evaluasiMateriId => $kriteriaArr) {
            if (!$materiValidIds->contains((int) $evaluasiMateriId) || $sudahDinilaiIds->contains((int) $evaluasiMateriId)) {
                continue; // lewati materi yang belum waktunya / sudah pernah dinilai
            }

            foreach ($kriteriaArr as $evaluasiFasilitatorId => $nilai) {
                EvaluasiFasilitatorJawaban::create([
                    'registrasi_id' => $registrasi->id,
                    'event_id' => $event->id,
                    'fasilitator_id' => $fasilitator->id,
                    'evaluasi_materi_id' => $evaluasiMateriId,
                    'evaluasi_fasilitator_id' => $evaluasiFasilitatorId,
                    'nilai' => $nilai,
                    'saran' => $request->saran,
                ]);
            }
            $adaTersimpan = true;
        }

        if (!$adaTersimpan) {
            return back()->with('error', 'Materi yang Anda kirim sudah pernah dinilai sebelumnya atau belum waktunya dinilai.');
        }

        return redirect()->route('evaluasi-pelatihan.public', ['uuid' => $uuid, 'registrasi' => $registrasi->id])
            ->with('success', 'Terima kasih! Evaluasi untuk ' . $fasilitator->nama_fasilitator . ' berhasil disimpan.');
    }
}