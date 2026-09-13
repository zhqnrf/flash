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
// Halaman Cetak Surat Bukti Event
    public function cetakSurat(Event $event)
    {
        // Pastikan relasi pelatihan ikut terpanggil
        $event->load('pelatihan');
        return view('event.cetak', compact('event'));
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
                'sertifikat_berlaku_mulai' => 'nullable|date',
'sertifikat_berlaku_selesai' => 'nullable|date|after_or_equal:sertifikat_berlaku_mulai',
                'lokasi' => 'nullable|string|max:255',
                'link_materi' => 'nullable|string',
                'has_presensi' => 'required|boolean',
                'warna_sertifikat' => 'nullable|string',
                'nomor_sertifikat' => 'nullable|string',
                'rekening_pembayaran' => 'nullable|string',
                'biaya_pelatihan' => 'nullable|numeric',
                // Data Array Pemetaan Fasilitator
                'fasilitator_materi' => 'nullable|array',
                'tanggal_sesi' => 'nullable|array',
            ]);

            $data = $request->except(['banner', 'fasilitator_materi', 'tanggal_sesi']);

            // Upload Banner jika ada
            if ($request->hasFile('banner')) {
                $data['banner'] = $request->file('banner')->store('event/banner', 'public');
            }

            // Create Event (Otomatis generate UUID karena Model Event sudah diset)
            $event = Event::create($data);

            // Simpan pemetaan Materi, Fasilitator, dan Tanggal Sesi (materi ini diajarkan tanggal berapa)
            if ($request->has('fasilitator_materi')) {
                foreach ($request->fasilitator_materi as $materi_id => $fasilitator_id) {
                    if (!empty($fasilitator_id)) {
                        EventFasilitatorMateri::create([
                            'event_id' => $event->id,
                            'evaluasi_materi_id' => $materi_id,
                            'fasilitator_id' => $fasilitator_id,
                            'tanggal_sesi' => $request->tanggal_sesi[$materi_id] ?? null,
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
        
        // Ambil pemetaan Fasilitator, Materi, & Tanggal Sesi yang tersimpan saat ini
        $currentMapping = EventFasilitatorMateri::where('event_id', $event->id)
                            ->get()
                            ->keyBy('evaluasi_materi_id');

        // Ambil seluruh materi dari pelatihan yang sedang aktif di event ini
        $materis = EvaluasiMateri::where('pelatihan_id', $event->pelatihan_id)->get();
        $materiData = [];
        
        foreach ($materis as $materi) {
            $fasilitators = Fasilitator::whereHas('materis', function($q) use ($materi) {
                $q->where('evaluasi_materis.id', $materi->id);
            })->get(['id', 'nama_fasilitator']);

            $mapping = $currentMapping->get($materi->id);

            $materiData[] = [
                'materi_id' => $materi->id,
                'nama_materi' => $materi->nama_materi,
                'fasilitators' => $fasilitators,
                'selected_fasilitator' => $mapping->fasilitator_id ?? null,
                'selected_tanggal_sesi' => $mapping && $mapping->tanggal_sesi ? $mapping->tanggal_sesi->format('Y-m-d') : null,
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
                'sertifikat_berlaku_mulai' => 'nullable|date',
'sertifikat_berlaku_selesai' => 'nullable|date|after_or_equal:sertifikat_berlaku_mulai',
                'has_presensi' => 'required|boolean',
            ]);

            $data = $request->except(['banner', 'fasilitator_materi', 'tanggal_sesi']);

            if ($request->hasFile('banner')) {
                if ($event->banner) Storage::disk('public')->delete($event->banner);
                $data['banner'] = $request->file('banner')->store('event/banner', 'public');
            }

            $event->update($data);

            // Update Pemetaan Fasilitator + Tanggal Sesi (Hapus lama, buat baru)
            if ($request->has('fasilitator_materi')) {
                EventFasilitatorMateri::where('event_id', $event->id)->delete();
                foreach ($request->fasilitator_materi as $materi_id => $fasilitator_id) {
                    if (!empty($fasilitator_id)) {
                        EventFasilitatorMateri::create([
                            'event_id' => $event->id,
                            'evaluasi_materi_id' => $materi_id,
                            'fasilitator_id' => $fasilitator_id,
                            'tanggal_sesi' => $request->tanggal_sesi[$materi_id] ?? null,
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
}