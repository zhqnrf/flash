<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registrasi;
use App\Models\SkillMateri;
use App\Models\PenilaianSkillJawaban;
use Illuminate\Http\Request;

class PenilaianSkillRekapController extends Controller
{
    public function index(Event $event)
    {
        $event->load('pelatihan');

        // 1. Dapatkan daftar materi + max poin untuk hitungan skala 100
        $materiList = $event->eventFasilitatorMateris()
            ->with(['evaluasiMateri', 'fasilitator'])
            ->get()
            ->filter(fn($efm) => $efm->evaluasiMateri)
            ->unique('evaluasi_materi_id')
            ->map(function ($efm) {
                $jumlahSkill = SkillMateri::where('evaluasi_materi_id', $efm->evaluasi_materi_id)->count();
                return [
                    'id' => $efm->evaluasi_materi_id,
                    'nama' => $efm->evaluasiMateri->nama_materi,
                    'ambang_batas' => (float) $efm->evaluasiMateri->ambang_batas,
                    'rentang_max' => (float) $efm->evaluasiMateri->rentang_nilai_max, // WAJIB UNTUK SKALA 100
                    'fasilitator_nama' => optional($efm->fasilitator)->nama_fasilitator,
                    'jumlah_skill' => $jumlahSkill,
                ];
            })->values();

        $totalSkillKeseluruhan = $materiList->sum('jumlah_skill');
        $semuaJawaban = PenilaianSkillJawaban::where('event_id', $event->id)->get();

        // 2. Olah data peserta & hitung skor asli skala 100 (Sinkron dengan Vue/Alpine)
        $pesertas = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->orderBy('nama')
            ->get()
            ->map(function ($p) use ($materiList, $semuaJawaban, $totalSkillKeseluruhan) {
                $jawabanPeserta = $semuaJawaban->where('registrasi_id', $p->id);

                $breakdownMateri = $materiList->map(function ($m) use ($jawabanPeserta) {
                    $jwb = $jawabanPeserta->where('evaluasi_materi_id', $m['id']);
                    $terjawab = $jwb->count();
                    
                    // RUMUS REAL-TIME: (Total Poin / (Jumlah Terjawab * Poin Max)) * 100
                    $rataRata = null;
                    if ($terjawab > 0 && $m['rentang_max'] > 0) {
                        $totalNilai = $jwb->sum('nilai');
                        $maxPoin = $terjawab * $m['rentang_max'];
                        $rataRata = round(($totalNilai / $maxPoin) * 100, 2);
                    }

                    $lengkap = $m['jumlah_skill'] > 0 && $terjawab >= $m['jumlah_skill'];
                    $lulus = $lengkap && !is_null($rataRata) ? $rataRata >= $m['ambang_batas'] : null;

                    return [
                        'materi_id' => $m['id'],
                        'nama_materi' => $m['nama'],
                        'jumlah_dinilai' => $terjawab,
                        'jumlah_skill' => $m['jumlah_skill'],
                        'lengkap' => $lengkap,
                        'rata_rata' => $rataRata,
                        'ambang_batas' => $m['ambang_batas'],
                        'lulus' => $lulus,
                    ];
                })->values();

                $totalTerisi = $jawabanPeserta->count();
                $lengkapSemua = $totalSkillKeseluruhan > 0 && $totalTerisi >= $totalSkillKeseluruhan;
                $lulusSemua = $breakdownMateri->isNotEmpty() && $breakdownMateri->every(fn($m) => $m['lulus'] === true);

                // Rata-rata keseluruhan dari skala 100 per materi
                $materiDikerjakan = $breakdownMateri->whereNotNull('rata_rata');
                $rataKeseluruhan = $materiDikerjakan->isNotEmpty() ? round($materiDikerjakan->avg('rata_rata'), 2) : null;

            return [
    'id' => $p->id,
    'uuid' => $p->uuid, // 👈 TAMBAHKAN BARIS INI
    'nama' => $p->nama_lengkap,
    'instansi' => $p->instansi,
    'total_terisi' => $totalTerisi,
    'total_skill' => $totalSkillKeseluruhan,
    'progress' => $totalSkillKeseluruhan > 0 ? round(($totalTerisi / $totalSkillKeseluruhan) * 100) : 0,
    'lengkap_semua' => $lengkapSemua,
    'rata_rata_keseluruhan' => $rataKeseluruhan,
    'lulus_semua' => $lengkapSemua ? $lulusSemua : null,
    'breakdown_materi' => $breakdownMateri,
];
            })
            // URUTKAN UNTUK LEADERBOARD (Tertinggi ke Terendah)
            ->sortByDesc(fn($p) => $p['rata_rata_keseluruhan'] ?? -1)
            ->values();

        $totalPeserta = $pesertas->count();
        $totalSudahLengkap = $pesertas->where('lengkap_semua', true)->count();
        $totalBelumLengkap = $totalPeserta - $totalSudahLengkap;
        $totalLulus = $pesertas->where('lulus_semua', true)->count();
        $rataRataEvent = $totalSudahLengkap > 0 ? round($pesertas->where('lengkap_semua', true)->avg('rata_rata_keseluruhan'), 2) : 0;

        // 3. Progres Fasilitator (Berapa peserta yang sudah diselesaikan per materi)
        $progressFasilitator = $materiList->map(function($m) use ($pesertas, $totalPeserta) {
            $selesaiCount = $pesertas->filter(function($p) use ($m) {
                $bm = $p['breakdown_materi']->firstWhere('materi_id', $m['id']);
                return $bm && $bm['lengkap'] === true;
            })->count();

            return [
                'materi' => $m['nama'],
                'fasilitator' => $m['fasilitator_nama'],
                'selesai' => $selesaiCount,
                'total' => $totalPeserta,
                'persen' => $totalPeserta > 0 ? round(($selesaiCount / $totalPeserta) * 100) : 0
            ];
        });

        // Chart Data per materi
        $rataPerMateri = $materiList->map(function ($m) use ($pesertas) {
            $kumpulanSkor = $pesertas->pluck('breakdown_materi')->flatten(1)
                ->where('materi_id', $m['id'])
                ->whereNotNull('rata_rata');
            return [
                'nama' => $m['nama'],
                'rata_rata' => $kumpulanSkor->isNotEmpty() ? round($kumpulanSkor->avg('rata_rata'), 2) : null,
                'ambang_batas' => $m['ambang_batas'],
            ];
        })->filter(fn($m) => !is_null($m['rata_rata']))->values();

        return view('event.penilaian_skill_rekap', compact(
            'event', 'materiList', 'pesertas', 'totalPeserta', 'totalSudahLengkap',
            'totalBelumLengkap', 'totalLulus', 'rataRataEvent', 'rataPerMateri', 'progressFasilitator'
        ));
    }

    public function reset(Event $event, Registrasi $registrasi)
    {
        PenilaianSkillJawaban::where('event_id', $event->id)->where('registrasi_id', $registrasi->id)->delete();
        return back()->with('success', 'Penilaian skill atas nama ' . $registrasi->nama_lengkap . ' berhasil direset.');
    }
}