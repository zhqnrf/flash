<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Pelatihan;
use App\Models\EvaluasiMateri;
use App\Models\Fasilitator;
use App\Models\EventFasilitatorMateri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // Halaman Create
    public function create()
    {
        $pelatihans = Pelatihan::all();
        return view('event.create', compact('pelatihans'));
    }

    // Endpoint AJAX: Ambil Materi & Fasilitator yang cocok berdasarkan Pelatihan
    public function getMateriFasilitator($pelatihan_id)
    {
        // Cari materi berdasarkan pelatihan (ingat, di DB kita buat pelatihan_id NOT NULL)
        $materis = EvaluasiMateri::where('pelatihan_id', $pelatihan_id)->get();
        
        $data = [];
        foreach ($materis as $materi) {
            // Cari fasilitator yang mengampu materi ini (lewat relasi HasMany/BelongsToMany)
            $fasilitators = Fasilitator::whereHas('materis', function($q) use ($materi) {
                $q->where('evaluasi_materis.id', $materi->id);
            })->get(['id', 'nama_fasilitator']);

            $data[] = [
                'materi_id' => $materi->id,
                'nama_materi' => $materi->nama_materi,
                'fasilitators' => $fasilitators
            ];
        }

        return response()->json($data);
    }

    // Simpan Data Event
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_event' => 'required|string|max:255',
                'batch' => 'nullable|string|max:50',
                'tahun' => 'required|string|max:4',
                'pelatihan_id' => 'required|exists:pelatihans,id',
                'tipe_pelatihan' => 'required|in:Workshop,Webinar,Pelatihan,Seminar',
                'sistem_pelatihan' => 'required|in:Daring,Luring,Blended',
                'link_zoom' => 'nullable|string', // Wajib disaring di view jika Daring/Blended
                'jenis_pelatihan' => 'required|in:Kerjasama,Mandiri',
                'instansi_penyelenggara' => 'nullable|string',
                'skp' => 'nullable|numeric',
                'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Max 2MB
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'waktu_presensi_mulai' => 'required',
                'waktu_presensi_selesai' => 'required',
                'lokasi' => 'nullable|string|max:255',
                'link_materi' => 'nullable|string',
                'has_presensi' => 'required|boolean',
                'warna_sertifikat' => 'nullable|string',
                'nomor_sertifikat' => 'nullable|string',
                'rekening_pembayaran' => 'nullable|string',
                'biaya_pelatihan' => 'nullable|numeric',
                // Data Array Pemetaan Fasilitator
                'fasilitator_materi' => 'nullable|array' 
            ]);

            $data = $request->except(['banner', 'fasilitator_materi']);

            // Upload Banner jika ada
            if ($request->hasFile('banner')) {
                $data['banner'] = $request->file('banner')->store('event/banner', 'public');
            }

            // Create Event (Otomatis generate UUID karena Model Event sudah diset)
            $event = Event::create($data);

            // Simpan pemetaan Materi dan Fasilitator
            if ($request->has('fasilitator_materi')) {
                foreach ($request->fasilitator_materi as $materi_id => $fasilitator_id) {
                    if (!empty($fasilitator_id)) {
                        EventFasilitatorMateri::create([
                            'event_id' => $event->id,
                            'evaluasi_materi_id' => $materi_id,
                            'fasilitator_id' => $fasilitator_id,
                        ]);
                    }
                }
            }

            // Redirect ke Index Event (Nanti kita buat halaman indexnya)
            return redirect()->route('event.index')->with('success', 'Event berhasil dibuat dan link otomatis di-generate!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    // Halaman Index
    public function index(Request $request)
    {
        $search = $request->input('search');
        $events = Event::with('pelatihan')
            ->when($search, function ($query, $search) {
                return $query->where('nama_event', 'like', "%{$search}%")
                             ->orWhere('batch', 'like', "%{$search}%")
                             ->orWhereHas('pelatihan', function($q) use ($search) {
                                 $q->where('nama_pelatihan', 'like', "%{$search}%");
                             });
            })
            ->latest()
            ->paginate(10);

        return view('event.index', compact('events'));
    }

    // Halaman Edit
    public function edit(Event $event)
    {
        $pelatihans = Pelatihan::all();
        
        // Ambil pemetaan Fasilitator & Materi yang tersimpan saat ini
        $currentMapping = EventFasilitatorMateri::where('event_id', $event->id)
                            ->pluck('fasilitator_id', 'evaluasi_materi_id')
                            ->toArray();

        // Ambil seluruh materi dari pelatihan yang sedang aktif di event ini
        $materis = EvaluasiMateri::where('pelatihan_id', $event->pelatihan_id)->get();
        $materiData = [];
        
        foreach ($materis as $materi) {
            $fasilitators = Fasilitator::whereHas('materis', function($q) use ($materi) {
                $q->where('evaluasi_materis.id', $materi->id);
            })->get(['id', 'nama_fasilitator']);

            $materiData[] = [
                'materi_id' => $materi->id,
                'nama_materi' => $materi->nama_materi,
                'fasilitators' => $fasilitators,
                'selected_fasilitator' => $currentMapping[$materi->id] ?? null
            ];
        }

        return view('event.edit', compact('event', 'pelatihans', 'materiData'));
    }

    // Proses Update
    public function update(Request $request, Event $event)
    {
        try {
            $request->validate([
                'nama_event' => 'required|string|max:255',
                'tahun' => 'required|string|max:4',
                'pelatihan_id' => 'required|exists:pelatihans,id',
                'tipe_pelatihan' => 'required|in:Workshop,Webinar,Pelatihan,Seminar',
                'sistem_pelatihan' => 'required|in:Daring,Luring,Blended',
                'jenis_pelatihan' => 'required|in:Kerjasama,Mandiri',
                'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'waktu_presensi_mulai' => 'required',
                'waktu_presensi_selesai' => 'required',
                'has_presensi' => 'required|boolean',
            ]);

            $data = $request->except(['banner', 'fasilitator_materi']);

            if ($request->hasFile('banner')) {
                if ($event->banner) Storage::disk('public')->delete($event->banner);
                $data['banner'] = $request->file('banner')->store('event/banner', 'public');
            }

            $event->update($data);

            // Update Pemetaan Fasilitator (Hapus lama, buat baru)
            if ($request->has('fasilitator_materi')) {
                EventFasilitatorMateri::where('event_id', $event->id)->delete();
                foreach ($request->fasilitator_materi as $materi_id => $fasilitator_id) {
                    if (!empty($fasilitator_id)) {
                        EventFasilitatorMateri::create([
                            'event_id' => $event->id,
                            'evaluasi_materi_id' => $materi_id,
                            'fasilitator_id' => $fasilitator_id,
                        ]);
                    }
                }
            }

            return redirect()->route('event.index')->with('success', 'Data Event berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Proses Delete
    public function destroy(Event $event)
    {
        if ($event->banner) Storage::disk('public')->delete($event->banner);
        $event->delete();
        return redirect()->route('event.index')->with('success', 'Event berhasil dihapus!');
    }
    // Nanti ditambahkan method index, edit, destroy dll di sini
}