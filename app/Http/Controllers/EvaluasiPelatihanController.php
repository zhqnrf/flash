<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiPelatihan;
use App\Models\Pelatihan;
use Illuminate\Http\Request;

class EvaluasiPelatihanController extends Controller
{
    public function exportBackend()
    {
        // Ambil semua data beserta relasi pelatihannya agar lengkap saat diexport
        $data = EvaluasiPelatihan::with('pelatihan')->get()->map(function($item) {
            return [
                'ID Pelatihan (Kosongkan jika global)' => $item->pelatihan_id,
                'Nama Pelatihan' => $item->pelatihan->nama_pelatihan ?? 'Semua Pelatihan (Global)',
                'Jenis Evaluasi' => $item->jenis_evaluasi,
                'Nama Evaluasi' => $item->nama_evaluasi,
                'Rentang Min' => $item->rentang_nilai_min,
                'Rentang Max' => $item->rentang_nilai_max,
            ];
        });

        return response()->json($data);
    }

    public function importBackend(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
        ]);

        $count = 0;
        foreach ($request->data as $row) {
            $jenis = $row['jenis_evaluasi'] ?? $row['Jenis Evaluasi'] ?? null;
            $nama = $row['nama_evaluasi'] ?? $row['Nama Evaluasi'] ?? null;
            $min = $row['rentang_nilai_min'] ?? $row['Rentang Min'] ?? 0;
            $max = $row['rentang_nilai_max'] ?? $row['Rentang Max'] ?? 100;
            $pelatihanId = !empty($row['ID Pelatihan (Kosongkan jika global)']) ? $row['ID Pelatihan (Kosongkan jika global)'] : (!empty($row['pelatihan_id']) ? $row['pelatihan_id'] : null);

            if (!empty($jenis) && !empty($nama)) {
                EvaluasiPelatihan::updateOrCreate(
                    [
                        'pelatihan_id' => $pelatihanId,
                        'jenis_evaluasi' => trim($jenis),
                        'nama_evaluasi' => trim($nama),
                    ],
                    [
                        'rentang_nilai_min' => $min,
                        'rentang_nilai_max' => $max,
                    ]
                );
                $count++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor $count data evaluasi pelatihan!"
        ]);
    }
    public function index(Request $request)
    {
        $query = EvaluasiPelatihan::with('pelatihan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jenis_evaluasi', 'like', "%{$search}%")
                  ->orWhere('nama_evaluasi', 'like', "%{$search}%")
                  ->orWhereHas('pelatihan', function($sub) use ($search) {
                      $sub->where('nama_pelatihan', 'like', "%{$search}%")
                          ->orWhere('nomor', 'like', "%{$search}%");
                  });
            });
        }

        $evaluasis = $query->orderBy('id', 'desc')->paginate(10)->appends($request->query());

        return view('master.evaluasi_pelatihan.index', compact('evaluasis'));
    }

    public function create()
    {
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        return view('master.evaluasi_pelatihan.create', compact('pelatihans'));
    }

   public function store(Request $request)
    {
        // Cek jika ini kiriman dari Import Excel (Batch)
        if ($request->has('batch_data')) {
            $importedCount = 0;
            foreach ($request->batch_data as $row) {
                $jenis = $row['jenis_evaluasi'] ?? $row['Jenis Evaluasi'] ?? null;
                $nama = $row['nama_evaluasi'] ?? $row['Nama Evaluasi'] ?? null;
                $min = $row['rentang_nilai_min'] ?? $row['Rentang Nilai Min'] ?? 0;
                $max = $row['rentang_nilai_max'] ?? $row['Rentang Nilai Max'] ?? 100;
                
                // Pelatihan ID bisa kosong (global) atau diisi angka
                $pelatihanId = !empty($row['pelatihan_id']) ? $row['pelatihan_id'] : null;

                if (!empty($jenis) && !empty($nama)) {
                    // UpdateOrCreate untuk mencegah duplikasi data yang sama persis
                    EvaluasiPelatihan::updateOrCreate(
                        [
                            'pelatihan_id' => $pelatihanId,
                            'jenis_evaluasi' => trim($jenis),
                            'nama_evaluasi' => trim($nama),
                        ],
                        [
                            'rentang_nilai_min' => $min,
                            'rentang_nilai_max' => $max,
                        ]
                    );
                    $importedCount++;
                }
            }
            return response()->json(['success' => true, 'message' => "Berhasil mengimpor $importedCount data evaluasi pelatihan."]);
        }

        // Input manual biasa
        $request->validate([
            'pelatihan_id' => 'nullable|exists:pelatihans,id',
            'jenis_evaluasi' => 'required|string|max:255',
            'nama_evaluasi' => 'required|string|max:255',
            'rentang_nilai_min' => 'required|numeric',
            'rentang_nilai_max' => 'required|numeric|gte:rentang_nilai_min',
        ]);

        EvaluasiPelatihan::create($request->all());

        return redirect()->route('master.evaluasi-pelatihan.index')->with('success', 'Master Evaluasi Pelatihan berhasil ditambahkan!');
    }
    public function edit(EvaluasiPelatihan $evaluasiPelatihan)
    {
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        return view('master.evaluasi_pelatihan.edit', compact('evaluasiPelatihan', 'pelatihans'));
    }

    public function update(Request $request, EvaluasiPelatihan $evaluasiPelatihan)
    {
        $request->validate([
    'pelatihan_id' => 'nullable|exists:pelatihans,id',
    'jenis_evaluasi' => 'required|string|max:255',
    'nama_evaluasi' => 'required|string|max:255',
    'rentang_nilai_min' => 'required|numeric',
    'rentang_nilai_max' => 'required|numeric|gte:rentang_nilai_min',
]);

        $evaluasiPelatihan->update($request->all());

        return redirect()->route('master.evaluasi-pelatihan.index')->with('success', 'Master Evaluasi Pelatihan berhasil diperbarui!');
    }

    public function destroy(EvaluasiPelatihan $evaluasiPelatihan)
    {
        $evaluasiPelatihan->delete();
        return redirect()->route('master.evaluasi-pelatihan.index')->with('success', 'Master Evaluasi Pelatihan berhasil dihapus!');
    }
}