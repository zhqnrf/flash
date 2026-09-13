<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Fasilitator;
use App\Models\EvaluasiMateri;
use App\Models\Registrasi;
use App\Models\SkillMateri;
use App\Models\PenilaianSkillJawaban;
use Illuminate\Http\Request;

class PenilaianSkillController extends Controller
{
    // ================================================================
    // HALAMAN PUBLIK: Fasilitator memilih peserta lalu menilai skill
    // peserta tersebut, per materi yang diampu (rubrik dari master
    // skill_materis di bawah evaluasi_materi ybs).
    // ================================================================
    public function create($uuid, $fasilitatorId, $materiId)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $fasilitator = Fasilitator::findOrFail($fasilitatorId);
        $materi = EvaluasiMateri::findOrFail($materiId);

        // Pastikan kombinasi event + fasilitator + materi memang valid/ditugaskan
        $efm = $event->eventFasilitatorMateris()
            ->where('fasilitator_id', $fasilitator->id)
            ->where('evaluasi_materi_id', $materi->id)
            ->first();

        abort_if(!$efm, 404, 'Anda tidak ditugaskan mengajar materi ini pada event tersebut.');

        // Rubrik skill untuk materi ini
        $skills = SkillMateri::where('evaluasi_materi_id', $materi->id)
            ->orderBy('id')
            ->get(['id', 'nama_skill', 'rentang_nilai_min', 'rentang_nilai_max']);

        // Daftar peserta yang diterima pada event ini
        $pesertas = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->orderBy('nama')
            ->get(['id', 'nama', 'gelar_depan', 'gelar_belakang', 'instansi'])
            ->map(fn($p) => [
                'id' => $p->id,
                'nama' => $p->nama_lengkap,
                'instansi' => $p->instansi,
            ]);

        // Map: registrasi_id => [skill_materi_id yang SUDAH dinilai fasilitator ini]
        $jawabanTersimpan = PenilaianSkillJawaban::where('event_id', $event->id)
            ->where('fasilitator_id', $fasilitator->id)
            ->where('evaluasi_materi_id', $materi->id)
            ->get();

        $sudahDinilaiMap = $jawabanTersimpan->groupBy('registrasi_id')
            ->map(fn($rows) => $rows->pluck('skill_materi_id')->unique()->values());

        // Berapa peserta yang SUDAH lengkap dinilai (semua skill terisi)
        $totalSkill = $skills->count();
        $totalPeserta = $pesertas->count();
        $sudahLengkapCount = $sudahDinilaiMap->filter(function ($skillIds) use ($totalSkill) {
            return $totalSkill > 0 && $skillIds->count() >= $totalSkill;
        })->count();
        $sisaPeserta = max($totalPeserta - $sudahLengkapCount, 0);

        $initialRegistrasiId = request()->query('registrasi');

        return view('public.evaluasi.penilaian_skill', compact(
            'event', 'fasilitator', 'materi', 'skills', 'pesertas',
            'sudahDinilaiMap', 'totalSkill', 'totalPeserta', 'sudahLengkapCount', 'sisaPeserta',
            'initialRegistrasiId'
        ));
    }

    public function store(Request $request, $uuid, $fasilitatorId, $materiId)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $fasilitator = Fasilitator::findOrFail($fasilitatorId);
        $materi = EvaluasiMateri::findOrFail($materiId);

        $request->validate([
            'registrasi_id' => 'required|exists:registrasis,id',
            'nilai' => 'required|array|min:1',
            'nilai.*' => 'required|integer|min:0',
        ], [
            'registrasi_id.required' => 'Silakan pilih nama peserta terlebih dahulu.',
            'nilai.required' => 'Semua skill wajib diberi nilai.',
        ]);

        $registrasi = Registrasi::where('id', $request->registrasi_id)
            ->where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->firstOrFail();

        $skillValidIds = SkillMateri::where('evaluasi_materi_id', $materi->id)->pluck('id');

        $sudahDinilaiIds = PenilaianSkillJawaban::where('registrasi_id', $registrasi->id)
            ->where('event_id', $event->id)
            ->where('evaluasi_materi_id', $materi->id)
            ->pluck('skill_materi_id');

        $tersimpan = 0;
        foreach ($request->nilai as $skillMateriId => $nilai) {
            if (!$skillValidIds->contains((int) $skillMateriId) || $sudahDinilaiIds->contains((int) $skillMateriId)) {
                continue; // lewati skill yang tidak valid / sudah pernah dinilai
            }

            PenilaianSkillJawaban::create([
                'registrasi_id' => $registrasi->id,
                'event_id' => $event->id,
                'fasilitator_id' => $fasilitator->id,
                'evaluasi_materi_id' => $materi->id,
                'skill_materi_id' => $skillMateriId,
                'nilai' => $nilai,
            ]);
            $tersimpan++;
        }

        if ($tersimpan === 0) {
            return back()->with('error', 'Peserta ini sudah pernah dinilai untuk seluruh skill pada materi ini.');
        }

        return redirect()->route('penilaian-skill.public', [$uuid, $fasilitatorId, $materiId])
            ->with('success', 'Nilai untuk ' . $registrasi->nama_lengkap . ' berhasil disimpan.');
    }
}