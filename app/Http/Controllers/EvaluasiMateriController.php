<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMateri;
use App\Models\Pelatihan;
use Illuminate\Http\Request;

class EvaluasiMateriController extends Controller
{
    public function exportBackend()
    {
        $data = EvaluasiMateri::with('pelatihan')->get()->map(function($item) {
            return [
                'ID Pelatihan' => $item->pelatihan_id,
                'Nama Pelatihan' => $item->pelatihan->nama_pelatihan ?? '-',
                'Nama Materi' => $item->nama_materi,
                'Unsur (Pisahkan koma misal: U1,U2)' => is_array($item->unsur) ? implode(',', $item->unsur) : $item->unsur,
                'Ambang Batas' => $item->ambang_batas,
                'Tema' => $item->tema,
                'Nilai Teori' => $item->nilai_teori,
                'Nilai Praktik' => $item->nilai_praktik,
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
                $namaMateri = $row['Nama Materi'] ?? $row['nama_materi'] ?? null;
                // Ambil Nama Pelatihan dari kolom Excel
                $namaPelatihan = $row['Nama Pelatihan'] ?? $row['nama_pelatihan'] ?? null;
                
                // Jika nama materi atau nama pelatihan kosong, lewati baris ini
                if (empty($namaMateri) || empty($namaPelatihan)) {
                    continue; 
                }

                // Cari Pelatihan berdasarkan nama (atau bisa juga pakai nomor pelatihan)
                $pelatihan = \App\Models\Pelatihan::where('nama_pelatihan', 'like', "%" . trim($namaPelatihan) . "%")->first();

                // Jika nama pelatihan tidak ditemukan di database, lewati baris ini
                if (!$pelatihan) {
                    continue; 
                }

                $unsurRaw = $row['Unsur (Pisahkan koma misal: U1,U2)'] ?? $row['unsur'] ?? 'U1';
                if (is_string($unsurRaw)) {
                    $unsurArray = array_map('trim', explode(',', $unsurRaw));
                } elseif (is_array($unsurRaw)) {
                    $unsurArray = $unsurRaw;
                } else {
                    $unsurArray = ['U1'];
                }

                $teori = intval($row['Nilai Teori'] ?? $row['nilai_teori'] ?? 0);
                $praktik = intval($row['Nilai Praktik'] ?? $row['nilai_praktik'] ?? 0);

                EvaluasiMateri::updateOrCreate(
                    [
                        'pelatihan_id' => $pelatihan->id, // Menggunakan ID hasil pencarian nama
                        'nama_materi' => trim($namaMateri),
                    ],
                    [
                        'unsur' => $unsurArray,
                        'ambang_batas' => $row['Ambang Batas'] ?? $row['ambang_batas'] ?? 70,
                        'tema' => $row['Tema'] ?? $row['tema'] ?? 'Kognitif',
                        'nilai_teori' => $teori,
                        'nilai_praktik' => $praktik,
                        'jpl' => $teori + $praktik,
                        'rentang_nilai_min' => $row['Rentang Min'] ?? $row['rentang_nilai_min'] ?? 0,
                        'rentang_nilai_max' => $row['Rentang Max'] ?? $row['rentang_nilai_max'] ?? 100,
                    ]
                );
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor $count data evaluasi materi!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage() . ' di baris ' . $e->getLine()
            ], 500);
        }
    }
    public function index()
    {
        // Tarik data evaluasi beserta relasi nama pelatihannya
        $materis = EvaluasiMateri::with('pelatihan')->orderBy('id', 'desc')->get();
        return view('master.evaluasi_materi.index', compact('materis'));
    }

    public function create()
    {
        // Ambil data pelatihan untuk dropdown "Nama/ID Pelatihan"
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        return view('master.evaluasi_materi.create', compact('pelatihans'));
    }
public function edit(EvaluasiMateri $evaluasiMateri)
    {
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();
        return view('master.evaluasi_materi.edit', compact('evaluasiMateri', 'pelatihans'));
    }

    public function update(Request $request, EvaluasiMateri $evaluasiMateri)
    {
        $request->validate([
            'pelatihan_id' => 'required|exists:pelatihans,id',
            'nama_materi' => 'required|string|max:255',
            'unsur' => 'required|array',
            'ambang_batas' => 'required|numeric',
            'tema' => 'required|string',
            'nilai_teori' => 'required|numeric',
            'nilai_praktik' => 'required|numeric',
            'rentang_nilai_min' => 'required|numeric',
            'rentang_nilai_max' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['jpl'] = $request->nilai_teori + $request->nilai_praktik;

        $evaluasiMateri->update($data);

        return redirect()->route('master.evaluasi-materi.index')->with('success', 'Master Evaluasi Materi berhasil diperbarui!');
    }

    public function destroy(EvaluasiMateri $evaluasiMateri)
    {
        $evaluasiMateri->delete();
        return redirect()->route('master.evaluasi-materi.index')->with('success', 'Master Evaluasi Materi berhasil dihapus!');
    }
    public function store(Request $request)
    {
        $request->validate([
            'pelatihan_id' => 'required|exists:pelatihans,id',
            'nama_materi' => 'required|string|max:255',
            'unsur' => 'required|array', // Validasi array karena multi-select
            'ambang_batas' => 'required|numeric',
            'tema' => 'required|string',
            'nilai_teori' => 'required|numeric',
            'nilai_praktik' => 'required|numeric',
            'rentang_nilai_min' => 'required|numeric',
            'rentang_nilai_max' => 'required|numeric',
        ]);

        $data = $request->all();
        
        // Kalkulasi otomatis JPL (Jamu Teori + Praktik) di backend sebagai validasi ganda
        $data['jpl'] = $request->nilai_teori + $request->nilai_praktik;

        EvaluasiMateri::create($data);

        return redirect()->route('master.evaluasi-materi.index')->with('success', 'Master Evaluasi Materi berhasil ditambahkan!');
    }
}