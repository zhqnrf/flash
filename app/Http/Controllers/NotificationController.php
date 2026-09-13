<?php

namespace App\Http\Controllers;

use App\Models\EventFasilitatorMateri;
use App\Models\Notifikasi;
use App\Models\PenilaianSkillJawaban;
use App\Models\Registrasi;
use App\Models\SkillMateri;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    // Endpoint AJAX dipanggil dari dropdown notifikasi di topbar
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        // 1. Notifikasi tersimpan (pendaftaran, pembayaran, ikm, pengaduan)
        $query = Notifikasi::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('pesan', 'like', "%{$search}%");
            });
        }

        $notifikasis = $query->latest()->limit(30)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'sumber' => 'notifikasi',
                'tipe' => $n->tipe,
                'ikon' => $n->ikon,
                'judul' => $n->judul,
                'pesan' => $n->pesan,
                'link' => $n->link,
                'is_read' => $n->is_read,
                'waktu' => $n->created_at->diffForHumans(),
            ];
        });

        // 2. Reminder live: fasilitator yang belum lengkap mengisi penilaian skill
        //    untuk materi yang tanggal sesinya sudah lewat/hari ini
        $reminderFasilitator = $this->reminderFasilitatorBelumIsi($search);

        return response()->json([
            'unread_count' => Notifikasi::belumDibaca()->count() + $reminderFasilitator->count(),
            'notifikasis' => $notifikasis,
            'reminder_fasilitator' => $reminderFasilitator,
        ]);
    }

    private function reminderFasilitatorBelumIsi(string $search)
    {
        $today = Carbon::today();

        $sesiList = EventFasilitatorMateri::with(['event', 'fasilitator', 'evaluasiMateri'])
            ->whereDate('tanggal_sesi', '<=', $today)
            ->whereNotNull('tanggal_sesi')
            ->get();

        $reminders = collect();

        foreach ($sesiList as $sesi) {
            if (!$sesi->evaluasiMateri || !$sesi->fasilitator || !$sesi->event) {
                continue;
            }

            $jumlahSkill = SkillMateri::where('evaluasi_materi_id', $sesi->evaluasi_materi_id)->count();
            $jumlahPeserta = Registrasi::where('event_id', $sesi->event_id)
                ->where('status_pendaftaran', 'Diterima')
                ->count();

            $totalDibutuhkan = $jumlahSkill * $jumlahPeserta;
            if ($totalDibutuhkan <= 0) {
                continue;
            }

            $totalTerisi = PenilaianSkillJawaban::where('event_id', $sesi->event_id)
                ->where('fasilitator_id', $sesi->fasilitator_id)
                ->where('evaluasi_materi_id', $sesi->evaluasi_materi_id)
                ->count();

            if ($totalTerisi >= $totalDibutuhkan) {
                continue; // sudah lengkap
            }

            $judul = 'Fasilitator Belum Mengisi';
            $pesan = "{$sesi->fasilitator->nama_fasilitator} belum lengkap mengisi penilaian skill materi \"{$sesi->evaluasiMateri->nama_materi}\" pada {$sesi->event->nama_event} ({$totalTerisi}/{$totalDibutuhkan}).";

            if ($search !== '' && stripos($judul . ' ' . $pesan, $search) === false) {
                continue;
            }

            $reminders->push([
                'id' => 'sesi-' . $sesi->id,
                'sumber' => 'reminder_fasilitator',
                'tipe' => 'fasilitator_belum_isi',
                'ikon' => 'user-clock',
                'judul' => $judul,
                'pesan' => $pesan,
                'link' => route('event.penilaian-skill', $sesi->event_id),
                'is_read' => false,
                'waktu' => Carbon::parse($sesi->tanggal_sesi)->diffForHumans(),
            ]);
        }

        return $reminders->values();
    }

    // Tandai satu notifikasi sudah dibaca, lalu arahkan ke link terkait
    public function read(Notifikasi $notifikasi)
    {
        $notifikasi->update(['is_read' => true]);

        return redirect($notifikasi->link ?? route('dashboard'));
    }

    // Tandai semua notifikasi tersimpan sudah dibaca (reminder fasilitator tidak butuh ini, sifatnya live)
    public function readAll()
    {
        Notifikasi::belumDibaca()->update(['is_read' => true]);

        return response()->json(['status' => 'ok']);
    }
}
