<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiFasilitator;
use App\Models\Pelatihan;
use Illuminate\Http\Request;

class EvaluasiFasilitatorController extends Controller
{
    public function exportBackend()
    {
        $data = EvaluasiFasilitator::with('pelatihan')->get()->map(function($item) {
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
        try {
            $request->validate([
                'data' => 'required|array',
            ]);

            $count = 0;
            foreach ($request->data as $row) {
                $jenis = $row['Jenis Evaluasi'] ?? $row['jenis_evaluasi'] ?? null;
                $nama = $row['Nama Evaluasi'] ?? $row['nama_evaluasi'] ?? null;
                
                // Ambil Nama Pelatihan (Opsional / Boleh Kosong)
                $namaPelatihan = $row['Nama Pelatihan'] ?? $row['nama_pelatihan'] ?? null;
                $pelatihanId = null;

                if (!empty($namaPelatihan)) {
                    // Cari pelatihan jika diisi
                    $pelatihan = \App\Models\Pelatihan::where('nama_pelatihan', 'like', "%" . trim($namaPelatihan) . "%")->first();
                    if ($pelatihan) {
                        $pelatihanId = $pelatihan->id;
                    }
                }

                $min = $row['Rentang Min'] ?? $row['rentang_nilai_min'] ?? 0;
                $max = $row['Rentang Max'] ?? $row['rentang_nilai_max'] ?? 100;

                if (!empty($jenis) && !empty($nama)) {
                    EvaluasiFasilitator::updateOrCreate(
                        [
                            'pelatihan_id' => $pelatihanId, // Bisa null (global) atau berisi ID jika ditemukan
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
                'message' => "Berhasil mengimpor $count data evaluasi fasilitator!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage() . ' di baris ' . $e->getLine()
            ], 500);
        }
    }
    public function index(Request $request)
    {
        $query = EvaluasiFasilitator::with('pelatihan');

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

        return view('master.evaluasi_fasilitator.index', compact('evaluasis'));
    }

    public function create()
    {
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        return view('master.evaluasi_fasilitator.create', compact('pelatihans'));
    }

    public function store(Request $request)
    {
     $request->validate([
    'pelatihan_id' => 'nullable|exists:pelatihans,id',
    'jenis_evaluasi' => 'required|string|max:255',
    'nama_evaluasi' => 'required|string|max:255',
    'rentang_nilai_min' => 'required|numeric',
    'rentang_nilai_max' => 'required|numeric|gte:rentang_nilai_min',
]);
        EvaluasiFasilitator::create($request->all());

        return redirect()->route('master.evaluasi-fasilitator.index')->with('success', 'Master Evaluasi Fasilitator berhasil ditambahkan!');
    }

    public function edit(EvaluasiFasilitator $evaluasiFasilitator)
    {
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        return view('master.evaluasi_fasilitator.edit', compact('evaluasiFasilitator', 'pelatihans'));
    }

    public function update(Request $request, EvaluasiFasilitator $evaluasiFasilitator)
    {
      $request->validate([
    'pelatihan_id' => 'nullable|exists:pelatihans,id',
    'jenis_evaluasi' => 'required|string|max:255',
    'nama_evaluasi' => 'required|string|max:255',
    'rentang_nilai_min' => 'required|numeric',
    'rentang_nilai_max' => 'required|numeric|gte:rentang_nilai_min',
]);

        $evaluasiFasilitator->update($request->all());

        return redirect()->route('master.evaluasi-fasilitator.index')->with('success', 'Master Evaluasi Fasilitator berhasil diperbarui!');
    }

    public function destroy(EvaluasiFasilitator $evaluasiFasilitator)
    {
        $evaluasiFasilitator->delete();
        return redirect()->route('master.evaluasi-fasilitator.index')->with('success', 'Master Evaluasi Fasilitator berhasil dihapus!');
    }
}