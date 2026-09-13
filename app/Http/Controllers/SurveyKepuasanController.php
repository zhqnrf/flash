<?php

namespace App\Http\Controllers;

use App\Models\SurveyUnsur;
use App\Models\SurveyKepuasan;
use App\Models\SurveyKepuasanJawaban;
use Illuminate\Http\Request;

class SurveyKepuasanController extends Controller
{
    // Halaman publik: form survey
    public function create()
    {
        $unsurs = SurveyUnsur::orderBy('urutan')->get();
        return view('public.survey.form', compact('unsurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_survey' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'usia' => 'required|integer|min:10|max:100',
            'pendidikan_terakhir' => 'required|string',
            'pendidikan_lainnya' => 'nullable|string|max:100',
            'pekerjaan' => 'required|string|max:100',
            'nilai' => 'required|array',
            'nilai.*' => 'required|integer|min:1|max:4',
        ]);

        $survey = SurveyKepuasan::create([
            'tanggal_survey' => $request->tanggal_survey,
            'jenis_kelamin' => $request->jenis_kelamin,
            'usia' => $request->usia,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'pendidikan_lainnya' => $request->pendidikan_terakhir === 'Yang lain' ? $request->pendidikan_lainnya : null,
            'pekerjaan' => $request->pekerjaan,
        ]);

        foreach ($request->nilai as $unsurId => $nilai) {
            SurveyKepuasanJawaban::create([
                'survey_kepuasan_id' => $survey->id,
                'survey_unsur_id' => $unsurId,
                'nilai' => $nilai,
            ]);
        }

        return back()->with('success', 'Terima kasih! Survey kepuasan Anda berhasil dikirim.');
    }

 // ... method create dan store ...

    // Method pembantu untuk menghitung IKM agar tidak mengulang kode
    private function hitungIKM($request)
    {
        $unsurs = SurveyUnsur::orderBy('urutan')->get();
        $query = SurveyKepuasan::with('jawabans.surveyUnsur')->orderBy('id');

        // Filter berdasarkan Bulan dan Tahun
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_survey', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_survey', $request->tahun);
        }

        $respondens = $query->get();
        $totalResponden = $respondens->count();

        // Hitung Demografi (Sesuai format PDF)
        $demografi = [
            'L' => $respondens->where('jenis_kelamin', 'Laki-laki')->count(),
            'P' => $respondens->where('jenis_kelamin', 'Perempuan')->count(),
            'Diploma' => $respondens->where('pendidikan_terakhir', 'Diploma (D1, D2, D3)')->count(),
            'Sarjana' => $respondens->where('pendidikan_terakhir', 'Sarjana (D4, S1)')->count(),
        ];

        $bobot = $unsurs->count() > 0 ? round(1 / $unsurs->count(), 2) : 0.11;

        $rekapUnsur = $unsurs->map(function ($u) use ($respondens, $bobot) {
            $semuaNilai = $respondens->flatMap(fn($r) => $r->jawabans)
                ->where('survey_unsur_id', $u->id)
                ->pluck('nilai');

            $nrr = $semuaNilai->isNotEmpty() ? round($semuaNilai->avg(), 3) : 0;

            return [
                'kode' => $u->kode,
                'nama_unsur' => $u->nama_unsur,
                'nrr' => $nrr,
                'nrr_tertimbang' => round($nrr * $bobot, 3),
            ];
        });

        $ikmTertimbang = round($rekapUnsur->sum('nrr_tertimbang'), 3);
        $ikm = round($ikmTertimbang * 25, 2);

        // Tentukan Mutu Pelayanan
        $mutu = 'D (Tidak Baik)';
        if ($ikm >= 88.31) $mutu = 'A (Sangat Baik)';
        elseif ($ikm >= 76.61) $mutu = 'B (Baik)';
        elseif ($ikm >= 65.00) $mutu = 'C (Kurang Baik)';

        $matriks = $respondens->map(function ($r, $i) use ($unsurs) {
            $baris = ['no' => $i + 1];
            foreach ($unsurs as $u) {
                $j = $r->jawabans->firstWhere('survey_unsur_id', $u->id);
                $baris[$u->kode] = $j->nilai ?? null;
            }
            return $baris;
        });

        return compact('unsurs', 'rekapUnsur', 'matriks', 'totalResponden', 'ikmTertimbang', 'ikm', 'bobot', 'mutu', 'demografi');
    }

    public function rekap(Request $request)
    {
        $data = $this->hitungIKM($request);
        return view('survey.rekap', $data);
    }

public function cetak(Request $request)
    {
        $data = $this->hitungIKM($request);
        $data['ttd_jenis'] = $request->ttd_jenis ?? 'manual';
        $data['periode'] = $request->tahun ?? date('Y');
        $data['bulan'] = $request->bulan ?? null;
        
        return view('survey.cetak', $data);
    }

    public function validasi($tahun, $bulan = null)
    {
        // Teks untuk bulan jika ada
        $namaBulan = $bulan ? date('F', mktime(0, 0, 0, $bulan, 1)) : 'Sepanjang Tahun';
        return view('survey.validasi', compact('tahun', 'namaBulan'));
    }
}