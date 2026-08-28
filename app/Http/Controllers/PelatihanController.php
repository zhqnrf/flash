<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use Illuminate\Http\Request;

class PelatihanController extends Controller
{

// Export Semua Data Pelatihan (Mengembalikan file untuk diunduh langsung atau diproses frontend)
    public function exportBackend()
    {
        $pelatihans = Pelatihan::all();
        return response()->json($pelatihans);
    }

    // Import Data Pelatihan di Backend (Anti-duplikasi dengan updateOrCreate)
    public function importBackend(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
        ]);

        $count = 0;
        foreach ($request->data as $row) {
            $nomor = $row['nomor'] ?? $row['Nomor'] ?? null;
            $nama = $row['nama_pelatihan'] ?? $row['Nama Pelatihan'] ?? null;

            if (!empty($nomor) && !empty($nama)) {
                Pelatihan::updateOrCreate(
                    ['nomor' => trim($nomor)],
                    ['nama_pelatihan' => trim($nama)]
                );
                $count++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor $count data pelatihan ke database!"
        ]);
    }
    // 1. Tampilkan List Pelatihan dengan Pencarian, Sort, & Paginasi
    public function index(Request $request)
    {
        $query = Pelatihan::query();

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                  ->orWhere('nama_pelatihan', 'like', "%{$search}%");
            });
        }

        // Fitur Pengurutan
        $sort = $request->input('sort', 'terbaru');
        if ($sort == 'az') {
            $query->orderBy('nama_pelatihan', 'asc');
        } elseif ($sort == 'za') {
            $query->orderBy('nama_pelatihan', 'desc');
        } elseif ($sort == 'terlama') {
            $query->orderBy('id', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $pelatihans = $query->paginate(10)->appends($request->query());

        return view('master.pelatihan.index', compact('pelatihans'));
    }

    // 2. Form Tambah Pelatihan
    public function create()
    {
        return view('master.pelatihan.create');
    }

 public function store(Request $request)
    {
        // Cek apakah ini kiriman dari Import Excel (Batch)
        if ($request->has('batch_data')) {
            $importedCount = 0;
            foreach ($request->batch_data as $row) {
                // Tangani berbagai kemungkinan nama header di excel (huruf besar/kecil/spasi)
                $nomor = $row['nomor'] ?? $row['Nomor'] ?? $row['NOMOR'] ?? null;
                $nama = $row['nama_pelatihan'] ?? $row['Nama Pelatihan'] ?? $row['NAMA PELATIHAN'] ?? null;

                if (!empty($nomor) && !empty($nama)) {
                    Pelatihan::updateOrCreate(
                        ['nomor' => trim($nomor)],
                        ['nama_pelatihan' => trim($nama)]
                    );
                    $importedCount++;
                }
            }
            return response()->json(['success' => true, 'message' => "Berhasil mengimpor $importedCount data."]);
        }

        // Jika ini input manual biasa dari form tambah
        $request->validate([
            'nomor' => 'required|string|max:255|unique:pelatihans',
            'nama_pelatihan' => 'required|string|max:255',
        ]);

        Pelatihan::create([
            'nomor' => $request->nomor,
            'nama_pelatihan' => $request->nama_pelatihan,
        ]);

        return redirect()->route('master.pelatihan.index')->with('success', 'Data pelatihan berhasil ditambahkan!');
    }

    // 4. Form Edit Pelatihan
    public function edit(Pelatihan $pelatihan)
    {
        return view('master.pelatihan.edit', compact('pelatihan'));
    }

    // 5. Update Pelatihan
    public function update(Request $request, Pelatihan $pelatihan)
    {
        $request->validate([
            'nomor' => 'required|string|max:255|unique:pelatihans,nomor,' . $pelatihan->id,
            'nama_pelatihan' => 'required|string|max:255',
        ]);

        $pelatihan->update([
            'nomor' => $request->nomor,
            'nama_pelatihan' => $request->nama_pelatihan,
        ]);

        return redirect()->route('master.pelatihan.index')->with('success', 'Data pelatihan berhasil diperbarui!');
    }

    // 6. Hapus Pelatihan
    public function destroy(Pelatihan $pelatihan)
    {
        $pelatihan->delete();
        return redirect()->route('master.pelatihan.index')->with('success', 'Data pelatihan berhasil dihapus!');
    }
}