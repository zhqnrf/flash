<?php

namespace App\Http\Controllers;

use App\Models\SkillMateri;
use App\Models\EvaluasiMateri;
use Illuminate\Http\Request;

class SkillMateriController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $skills = SkillMateri::with('evaluasiMateri')
            ->when($search, function ($query, $search) {
                return $query->where('nama_skill', 'like', "%{$search}%")
                             ->orWhereHas('evaluasiMateri', function($q) use ($search) {
                                 $q->where('nama_materi', 'like', "%{$search}%");
                             });
            })
            ->latest()
            ->paginate(10);

        $materis = EvaluasiMateri::all();
        return view('master.skill_materi.index', compact('skills', 'materis'));
    }

    public function create()
    {
        $materis = EvaluasiMateri::all();
        return view('master.skill_materi.create', compact('materis'));
    }



    public function edit(SkillMateri $skillMateri)
    {
        $materis = EvaluasiMateri::all();
        return view('master.skill_materi.edit', compact('skillMateri', 'materis'));
    }

   public function store(Request $request)
    {
        try {
            $request->validate([
                'evaluasi_materi_id' => 'required|exists:evaluasi_materis,id',
                'nama_skill' => 'required|string|max:255',
            ]);

            // Ambil data master materi untuk mendapatkan rentang nilai otomatis
            $materi = EvaluasiMateri::findOrFail($request->evaluasi_materi_id);

            SkillMateri::create([
                'evaluasi_materi_id' => $request->evaluasi_materi_id,
                'nama_skill' => $request->nama_skill,
                'rentang_nilai_min' => $materi->rentang_nilai_min ?? 0,
                'rentang_nilai_max' => $materi->rentang_nilai_max ?? 100,
            ]);

            return redirect()->route('master.skill-materi.index')->with('success', 'Data skill materi berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, SkillMateri $skillMateri)
    {
        try {
            $request->validate([
                'evaluasi_materi_id' => 'required|exists:evaluasi_materis,id',
                'nama_skill' => 'required|string|max:255',
            ]);

            // Ambil ulang rentang nilai jika materinya diubah
            $materi = EvaluasiMateri::findOrFail($request->evaluasi_materi_id);

            $skillMateri->update([
                'evaluasi_materi_id' => $request->evaluasi_materi_id,
                'nama_skill' => $request->nama_skill,
                'rentang_nilai_min' => $materi->rentang_nilai_min ?? 0,
                'rentang_nilai_max' => $materi->rentang_nilai_max ?? 100,
            ]);

            return redirect()->route('master.skill-materi.index')->with('success', 'Data skill materi berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(SkillMateri $skillMateri)
    {
        $skillMateri->delete();
        return redirect()->route('master.skill-materi.index')->with('success', 'Data skill materi berhasil dihapus!');
    }

    public function exportBackend()
    {
        $data = SkillMateri::with('evaluasiMateri')->get()->map(function($item) {
            return [
                'Nama Materi Pelatihan' => $item->evaluasiMateri->nama_materi ?? '-',
                'Nama Skill' => $item->nama_skill,
                'Rentang Nilai Min' => $item->rentang_nilai_min,
                'Rentang Nilai Max' => $item->rentang_nilai_max,
            ];
        });

        return response()->json($data);
    }

    public function importBackend(Request $request)
    {
        try {
            $request->validate(['data' => 'required|array']);
            $count = 0;

            foreach ($request->data as $row) {
                $namaMateri = $row['Nama Materi Pelatihan'] ?? $row['nama_materi'] ?? null;
                $namaSkill = $row['Nama Skill'] ?? $row['nama_skill'] ?? null;

                if (empty($namaMateri) || empty($namaSkill)) continue;

                // Cari materi berdasarkan nama
                $materi = EvaluasiMateri::where('nama_materi', 'like', "%" . trim($namaMateri) . "%")->first();
                if (!$materi) continue;

                SkillMateri::updateOrCreate(
                    [
                        'evaluasi_materi_id' => $materi->id,
                        'nama_skill' => trim($namaSkill),
                    ],
                    [
                        'rentang_nilai_min' => $row['Rentang Nilai Min'] ?? $row['rentang_nilai_min'] ?? 1,
                        'rentang_nilai_max' => $row['Rentang Nilai Max'] ?? $row['rentang_nilai_max'] ?? 4,
                    ]
                );
                $count++;
            }

            return response()->json(['success' => true, 'message' => "Berhasil mengimpor $count data skill materi!"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}