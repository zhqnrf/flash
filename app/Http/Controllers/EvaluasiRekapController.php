<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registrasi;
use App\Models\EvaluasiPelatihan;
use App\Models\EvaluasiFasilitatorJawaban;
use App\Models\EvaluasiMateriJawaban;

class EvaluasiRekapController extends Controller
{
    public function index(Event $event)
    {
        $event->load('pelatihan');

        // ============ DATA PESERTA + JAWABAN EVALUASI PELATIHAN ============
        $pesertas = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->with('evaluasiPelatihanJawabans.evaluasiPelatihan')
            ->orderBy('nama')
            ->get()
            ->map(function ($p) {
                $jawaban = $p->evaluasiPelatihanJawabans;
                $p->sudah_isi = $jawaban->isNotEmpty();
                $p->rata_rata = $jawaban->isNotEmpty() ? round($jawaban->avg('nilai'), 2) : null;
                $p->tanggal_isi = $jawaban->isNotEmpty() ? $jawaban->max('created_at') : null;
                $p->saran = $jawaban->isNotEmpty() ? $jawaban->first()->saran : null;
                $p->detail_jawaban = $jawaban->map(fn($j) => [
                    'kriteria' => optional($j->evaluasiPelatihan)->nama_evaluasi ?? '-',
                    'nilai' => $j->nilai,
                ])->values();
                return $p;
            });

        $totalDiterima = $pesertas->count();
        $totalSudahIsi = $pesertas->where('sudah_isi', true)->count();
        $totalBelumIsi = $totalDiterima - $totalSudahIsi;
        $rataRataKeseluruhan = $totalSudahIsi > 0
            ? round($pesertas->where('sudah_isi', true)->avg('rata_rata'), 2)
            : 0;

        // ============ RATA-RATA PER KRITERIA (dashboard) ============
        $kriteriaList = EvaluasiPelatihan::where(function ($q) use ($event) {
            $q->where('pelatihan_id', $event->pelatihan_id)->orWhereNull('pelatihan_id');
        })->orderBy('id')->get();

        $rataPerKriteria = $kriteriaList->map(function ($k) use ($pesertas) {
            $semuaNilai = $pesertas->flatMap(fn($p) => $p->detail_jawaban)
                ->where('kriteria', $k->nama_evaluasi)
                ->pluck('nilai');

            return [
                'nama' => $k->nama_evaluasi,
                'rata_rata' => $semuaNilai->isNotEmpty() ? round($semuaNilai->avg(), 2) : null,
                'maksimal' => $k->rentang_nilai_max,
            ];
        })->filter(fn($k) => !is_null($k['rata_rata']))->values();

        // ============ DAFTAR SARAN PESERTA (Evaluasi Pelatihan) ============
        $daftarSaran = $pesertas->filter(fn($p) => !empty(trim($p->saran ?? '')))
            ->map(fn($p) => [
                'nama' => $p->nama_lengkap,
                'instansi' => $p->instansi,
                'saran' => $p->saran,
                'tanggal' => $p->tanggal_isi ? $p->tanggal_isi->format('d M Y H:i') : '-',
            ])->values();

        // ============ REKAP PER FASILITATOR: 2 KOMPONEN TERPISAH ============
        $rekapFasilitator = $event->eventFasilitatorMateris()
            ->with(['fasilitator', 'evaluasiMateri'])
            ->get()
            ->filter(fn($efm) => $efm->fasilitator)
            ->groupBy('fasilitator_id')
            ->map(function ($items) use ($event) {
                $first = $items->first();

                // --- Komponen Fasilitator (1x per peserta, tidak terikat materi) ---
                $jawabanFasilitator = EvaluasiFasilitatorJawaban::where('event_id', $event->id)
                    ->where('fasilitator_id', $first->fasilitator_id)
                    ->with(['registrasi', 'evaluasiFasilitator'])
                    ->get();

                $pesertaDetailFasilitator = $jawabanFasilitator->groupBy('registrasi_id')->map(function ($jwb) {
                    $reg = optional($jwb->first())->registrasi;
                    return [
                        'nama' => $reg ? $reg->nama_lengkap : '-',
                        'instansi' => $reg ? $reg->instansi : '-',
                        'rata_rata' => round($jwb->avg('nilai'), 2),
                        'saran' => $jwb->first()->saran,
                        'jawaban' => $jwb->map(fn($j) => [
                            'kriteria' => optional($j->evaluasiFasilitator)->nama_evaluasi ?? '-',
                            'nilai' => $j->nilai,
                        ])->values(),
                    ];
                })->values();

                // --- Komponen Materi (per materi yang dia ajarkan) ---
                $jawabanMateri = EvaluasiMateriJawaban::where('event_id', $event->id)
                    ->where('fasilitator_id', $first->fasilitator_id)
                    ->with(['registrasi', 'evaluasiMateri'])
                    ->get();

                $materiBreakdown = $items->pluck('evaluasiMateri')->filter()->unique('id')->map(function ($materi) use ($jawabanMateri) {
                    $jwbMateriIni = $jawabanMateri->where('evaluasi_materi_id', $materi->id);
                    return [
                        'nama_materi' => $materi->nama_materi,
                        'jumlah_menilai' => $jwbMateriIni->count(),
                        'rata_rata' => $jwbMateriIni->isNotEmpty() ? round($jwbMateriIni->avg('nilai'), 2) : null,
                    ];
                })->values();

                $pesertaDetailMateri = $jawabanMateri->groupBy('registrasi_id')->map(function ($jwb) {
                    $reg = optional($jwb->first())->registrasi;
                    return [
                        'nama' => $reg ? $reg->nama_lengkap : '-',
                        'instansi' => $reg ? $reg->instansi : '-',
                        'jawaban' => $jwb->map(fn($j) => [
                            'materi' => optional($j->evaluasiMateri)->nama_materi ?? '-',
                            'nilai' => $j->nilai,
                        ])->values(),
                        'saran' => $jwb->first()->saran,
                    ];
                })->values();

                return [
                    'id' => $first->fasilitator_id,
                    'nama' => $first->fasilitator->nama_fasilitator,
                    'materi' => $items->pluck('evaluasiMateri.nama_materi')->filter()->unique()->values(),
                    // Komponen Fasilitator
                    'jumlah_menilai_fasilitator' => $pesertaDetailFasilitator->count(),
                    'rata_rata_fasilitator' => $jawabanFasilitator->isNotEmpty() ? round($jawabanFasilitator->avg('nilai'), 2) : null,
                    'peserta_detail_fasilitator' => $pesertaDetailFasilitator,
                    // Komponen Materi
                    'materi_breakdown' => $materiBreakdown,
                    'rata_rata_materi' => $jawabanMateri->isNotEmpty() ? round($jawabanMateri->avg('nilai'), 2) : null,
                    'peserta_detail_materi' => $pesertaDetailMateri,
                ];
            })->values();

        return view('event.evaluasi', compact(
            'event', 'pesertas', 'totalDiterima', 'totalSudahIsi', 'totalBelumIsi', 'rataRataKeseluruhan',
            'rataPerKriteria', 'daftarSaran', 'rekapFasilitator'
        ));
    }
}