<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registrasi;
use App\Models\Fasilitator;
use App\Models\EvaluasiPelatihan;
use App\Models\EvaluasiFasilitator;
use App\Models\EvaluasiPelatihanJawaban;
use App\Models\EvaluasiFasilitatorJawaban;
use App\Models\EvaluasiMateriJawaban;
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

        // Map: registrasi_id => [fasilitator_id yang KOMPONEN FASILITATOR-nya sudah dievaluasi]
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

    // Admin: hapus evaluasi pelatihan seorang peserta (supaya bisa isi ulang)
    public function hapusEvaluasiPelatihan(Registrasi $registrasi)
    {
        EvaluasiPelatihanJawaban::where('registrasi_id', $registrasi->id)->delete();
        return back()->with('success', 'Evaluasi pelatihan atas nama ' . $registrasi->nama_lengkap . ' berhasil dihapus.');
    }

    // ================================================================
    // HALAMAN EVALUASI KHUSUS 1 FASILITATOR
    // Terdiri dari 2 komponen terpisah:
    //  1) Komponen MATERI (dari master EvaluasiMateri) -> dinilai PER MATERI, digate per tanggal_sesi
    //  2) Komponen FASILITATOR (dari master EvaluasiFasilitator) -> dinilai 1x saja per fasilitator
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

        // ---- Komponen Fasilitator: kriteria + siapa saja yang SUDAH menilai (1x per peserta) ----
        $kriteriaFasilitator = EvaluasiFasilitator::where(function ($q) use ($event) {
            $q->where('pelatihan_id', $event->pelatihan_id)->orWhereNull('pelatihan_id');
        })->orderBy('id')->get();

        $sudahNilaiFasilitatorIds = EvaluasiFasilitatorJawaban::where('event_id', $event->id)
            ->where('fasilitator_id', $fasilitator->id)
            ->pluck('registrasi_id')->unique();

        // ---- Komponen Materi: daftar materi yang sudah "waktunya" (tanggal_sesi <= hari ini) ----
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
                'rentang_min' => $efm->evaluasiMateri->rentang_nilai_min,
                'rentang_max' => $efm->evaluasiMateri->rentang_nilai_max,
                'tanggal_sesi' => optional($efm->tanggal_sesi)->translatedFormat('d M Y'),
            ])->values();

        $totalMateriKeseluruhan = $event->eventFasilitatorMateris()
            ->where('fasilitator_id', $fasilitator->id)
            ->distinct('evaluasi_materi_id')
            ->count('evaluasi_materi_id');

        // Map: registrasi_id => [evaluasi_materi_id yang SUDAH dinilai peserta itu]
        $sudahDinilaiMateriMap = EvaluasiMateriJawaban::where('event_id', $event->id)
            ->where('fasilitator_id', $fasilitator->id)
            ->get()
            ->groupBy('registrasi_id')
            ->map(fn($rows) => $rows->pluck('evaluasi_materi_id')->unique()->values());

        $initialRegistrasiId = request()->query('registrasi');

        return view('public.evaluasi.fasilitator', compact(
            'event', 'fasilitator', 'pesertas',
            'kriteriaFasilitator', 'sudahNilaiFasilitatorIds',
            'materiTersedia', 'totalMateriKeseluruhan', 'sudahDinilaiMateriMap',
            'initialRegistrasiId'
        ));
    }

    public function storeFasilitator(Request $request, $uuid, $fasilitatorId)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $fasilitator = Fasilitator::findOrFail($fasilitatorId);

        $request->validate([
            'registrasi_id' => 'required|exists:registrasis,id',
            'nilai_fasilitator' => 'nullable|array',
            'nilai_materi' => 'nullable|array',
            'saran' => 'nullable|string|max:2000',
        ], [
            'registrasi_id.required' => 'Silakan cari dan pilih nama Anda terlebih dahulu.',
        ]);

        $registrasi = Registrasi::where('id', $request->registrasi_id)
            ->where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->firstOrFail();

        $adaTersimpan = false;
        $hariIni = now()->toDateString();

        // ---- Simpan Komponen Fasilitator (hanya jika belum pernah dinilai peserta ini) ----
        if ($request->filled('nilai_fasilitator')) {
            $sudahNilaiFasilitator = EvaluasiFasilitatorJawaban::where('registrasi_id', $registrasi->id)
                ->where('fasilitator_id', $fasilitator->id)
                ->where('event_id', $event->id)->exists();

            if (!$sudahNilaiFasilitator) {
                foreach ($request->nilai_fasilitator as $evaluasiFasilitatorId => $nilai) {
                    EvaluasiFasilitatorJawaban::create([
                        'registrasi_id' => $registrasi->id,
                        'event_id' => $event->id,
                        'fasilitator_id' => $fasilitator->id,
                        'evaluasi_fasilitator_id' => $evaluasiFasilitatorId,
                        'nilai' => $nilai,
                        'saran' => $request->saran,
                    ]);
                }
                $adaTersimpan = true;
            }
        }

        // ---- Simpan Komponen Materi (per materi, hanya yang valid & belum dinilai) ----
        if ($request->filled('nilai_materi')) {
            $materiValidIds = $event->eventFasilitatorMateris()
                ->where('fasilitator_id', $fasilitator->id)
                ->get()
                ->filter(fn($efm) => is_null($efm->tanggal_sesi) || $efm->tanggal_sesi->toDateString() <= $hariIni)
                ->pluck('evaluasi_materi_id')->unique();

            $sudahDinilaiIds = EvaluasiMateriJawaban::where('registrasi_id', $registrasi->id)
                ->where('fasilitator_id', $fasilitator->id)
                ->where('event_id', $event->id)
                ->pluck('evaluasi_materi_id')->unique();

            foreach ($request->nilai_materi as $evaluasiMateriId => $nilai) {
                if (!$materiValidIds->contains((int) $evaluasiMateriId) || $sudahDinilaiIds->contains((int) $evaluasiMateriId)) {
                    continue; // lewati materi yang belum waktunya / sudah pernah dinilai
                }

                EvaluasiMateriJawaban::create([
                    'registrasi_id' => $registrasi->id,
                    'event_id' => $event->id,
                    'fasilitator_id' => $fasilitator->id,
                    'evaluasi_materi_id' => $evaluasiMateriId,
                    'nilai' => $nilai,
                    'saran' => $request->saran,
                ]);
                $adaTersimpan = true;
            }
        }

        if (!$adaTersimpan) {
            return back()->with('error', 'Tidak ada penilaian baru yang tersimpan. Kemungkinan sudah pernah dinilai sebelumnya atau belum waktunya.');
        }

        return redirect()->route('evaluasi-pelatihan.public', ['uuid' => $uuid, 'registrasi' => $registrasi->id])
            ->with('success', 'Terima kasih! Evaluasi untuk ' . $fasilitator->nama_fasilitator . ' berhasil disimpan.');
    }
}