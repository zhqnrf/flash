<?php

namespace App\Http\Controllers;

use App\Models\Fasilitator;
use App\Models\EvaluasiMateri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FasilitatorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $fasilitators = Fasilitator::with('materis')
            ->when($search, function ($query, $search) {
                return $query->where('nama_fasilitator', 'like', "%{$search}%")
                             ->orWhere('profesi_fasilitator', 'like', "%{$search}%")
                             ->orWhere('tempat_kerja_fasilitator', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('master.fasilitator.index', compact('fasilitators'));
    }

    public function create()
    {
        $materis = EvaluasiMateri::all();
        return view('master.fasilitator.create', compact('materis'));
    }

 public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_fasilitator' => 'required|string|max:255',
                'profesi_fasilitator' => 'nullable|string|max:255',
                'jabatan_fasilitator' => 'nullable|string|max:255',
                'tempat_kerja_fasilitator' => 'nullable|string|max:255',
                'pendidikan_terakhir_fasilitator' => 'nullable|string|max:255',
                'dokumen_fasilitator' => 'nullable|file|mimes:pdf,doc,docx|max:1024', // Maksimal 1MB
                'foto_fasilitator' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',    // Maksimal 1MB
                'materi_ids' => 'nullable|array',
            ], [
                'dokumen_fasilitator.max' => 'Ukuran dokumen terlalu besar! Maksimal ukuran file adalah 1 MB.',
                'dokumen_fasilitator.mimes' => 'Format dokumen harus berformat PDF, DOC, atau DOCX.',
                'foto_fasilitator.max' => 'Ukuran foto terlalu besar! Maksimal ukuran foto adalah 1 MB.',
                'foto_fasilitator.image' => 'File foto harus berupa gambar (JPG, JPEG, PNG).',
            ]);

            $data = $request->except(['dokumen_fasilitator', 'foto_fasilitator', 'materi_ids']);

            if ($request->hasFile('dokumen_fasilitator')) {
                $data['dokumen_fasilitator'] = $request->file('dokumen_fasilitator')->store('fasilitator/dokumen', 'public');
            }

            if ($request->hasFile('foto_fasilitator')) {
                $data['foto_fasilitator'] = $request->file('foto_fasilitator')->store('fasilitator/foto', 'public');
            }

            $fasilitator = Fasilitator::create($data);

            if ($request->has('materi_ids')) {
                $fasilitator->materis()->sync($request->materi_ids);
            }

            return redirect()->route('master.fasilitator.index')->with('success', 'Data fasilitator berhasil ditambahkan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ambil pesan error validasi pertama untuk ditampilkan
            $errorMessage = collect($e->errors())->flatten()->first();
            return redirect()->back()->withInput()->with('error', $errorMessage);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

 public function edit(Fasilitator $fasilitator)
    {
        $materis = EvaluasiMateri::all();
        return view('master.fasilitator.edit', compact('fasilitator', 'materis'));
    }

    public function update(Request $request, Fasilitator $fasilitator)
    {
        $request->validate([
            'nama_fasilitator' => 'required|string|max:255',
            'profesi_fasilitator' => 'nullable|string|max:255',
            'jabatan_fasilitator' => 'nullable|string|max:255',
            'tempat_kerja_fasilitator' => 'nullable|string|max:255',
            'pendidikan_terakhir_fasilitator' => 'nullable|string|max:255',
            'dokumen_fasilitator' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'foto_fasilitator' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'materi_ids' => 'nullable|array',
        ]);

        $data = $request->except(['dokumen_fasilitator', 'foto_fasilitator', 'materi_ids']);

        if ($request->hasFile('dokumen_fasilitator')) {
            if ($fasilitator->dokumen_fasilitator) Storage::disk('public')->delete($fasilitator->dokumen_fasilitator);
            $data['dokumen_fasilitator'] = $request->file('dokumen_fasilitator')->store('fasilitator/dokumen', 'public');
        }

        if ($request->hasFile('foto_fasilitator')) {
            if ($fasilitator->foto_fasilitator) Storage::disk('public')->delete($fasilitator->foto_fasilitator);
            $data['foto_fasilitator'] = $request->file('foto_fasilitator')->store('fasilitator/foto', 'public');
        }

        $fasilitator->update($data);
        $fasilitator->materis()->sync($request->materi_ids ?? []);

        return redirect()->route('master.fasilitator.index')->with('success', 'Data fasilitator berhasil diperbarui!');
    }

    public function destroy(Fasilitator $fasilitator)
    {
        if ($fasilitator->dokumen_fasilitator) Storage::disk('public')->delete($fasilitator->dokumen_fasilitator);
        if ($fasilitator->foto_fasilitator) Storage::disk('public')->delete($fasilitator->foto_fasilitator);
        
        $fasilitator->materis()->detach();
        $fasilitator->delete();

        return redirect()->route('master.fasilitator.index')->with('success', 'Data fasilitator berhasil dihapus!');
    }



    public function exportBackend()
    {
        $data = Fasilitator::with('materis')->get()->map(function($item) {
            return [
                'Nama Fasilitator' => $item->nama_fasilitator,
                'Profesi' => $item->profesi_fasilitator,
                'Jabatan' => $item->jabatan_fasilitator,
                'Tempat Kerja' => $item->tempat_kerja_fasilitator,
                'Pendidikan Terakhir' => $item->pendidikan_terakhir_fasilitator,
                'Materi Yang Diampu (Pisahkan koma)' => $item->materis->pluck('nama_materi')->implode(', '),
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
                $nama = $row['Nama Fasilitator'] ?? $row['nama_fasilitator'] ?? null;
                if (empty($nama)) continue;

                $fasilitator = Fasilitator::updateOrCreate(
                    ['nama_fasilitator' => trim($nama)],
                    [
                        'profesi_fasilitator' => $row['Profesi'] ?? $row['profesi_fasilitator'] ?? null,
                        'jabatan_fasilitator' => $row['Jabatan'] ?? $row['jabatan_fasilitator'] ?? null,
                        'tempat_kerja_fasilitator' => $row['Tempat Kerja'] ?? $row['tempat_kerja_fasilitator'] ?? null,
                        'pendidikan_terakhir_fasilitator' => $row['Pendidikan Terakhir'] ?? $row['pendidikan_terakhir_fasilitator'] ?? null,
                    ]
                );

                // Hubungkan materi jika ada di kolom excel (berdasarkan nama materi)
                $materiStr = $row['Materi Yang Diampu (Pisahkan koma)'] ?? $row['materi'] ?? null;
                if (!empty($materiStr)) {
                    $materiNames = array_map('trim', explode(',', $materiStr));
                    $materiIds = EvaluasiMateri::whereIn('nama_materi', $materiNames)->pluck('id')->toArray();
                    $fasilitator->materis()->sync($materiIds);
                }
                $count++;
            }

            return response()->json(['success' => true, 'message' => "Berhasil mengimpor $count data fasilitator!"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}