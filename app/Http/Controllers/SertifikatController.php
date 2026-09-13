<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registrasi;
use App\Models\SkillMateri;
use App\Models\PenilaianSkillJawaban;

class SertifikatController extends Controller
{
    // Susun rincian materi (tema, unsur, JP teori/praktik/jpl, nilai, status lulus)
    // untuk SATU peserta pada event tertentu. Dipakai di halaman sertifikat (hal. 2)
    // maupun bisa dipakai admin untuk cek kelulusan.
    private function susunRincianMateri(Event $event, Registrasi $registrasi)
    {
        $materiList = $event->eventFasilitatorMateris()
            ->with(['evaluasiMateri', 'fasilitator'])
            ->get()
            ->filter(fn($efm) => $efm->evaluasiMateri)
            ->unique('evaluasi_materi_id')
            ->values();

        $jawabanPeserta = PenilaianSkillJawaban::where('event_id', $event->id)
            ->where('registrasi_id', $registrasi->id)
            ->get();

        return $materiList->map(function ($efm) use ($jawabanPeserta) {
            $materi = $efm->evaluasiMateri;
            $jumlahSkill = SkillMateri::where('evaluasi_materi_id', $materi->id)->count();
            $jwb = $jawabanPeserta->where('evaluasi_materi_id', $materi->id);
            $lengkap = $jumlahSkill > 0 && $jwb->count() >= $jumlahSkill;
            $rataRata = $jwb->isNotEmpty() ? round($jwb->avg('nilai'), 2) : null;
            $lulus = $lengkap && !is_null($rataRata) ? $rataRata >= (float) $materi->ambang_batas : false;

            return [
                'nama_materi' => $materi->nama_materi,
                'tema' => $materi->tema,
                'unsur' => is_array($materi->unsur) ? implode(', ', $materi->unsur) : $materi->unsur,
                'nilai_teori' => $materi->nilai_teori,
                'nilai_praktik' => $materi->nilai_praktik,
                'jpl' => $materi->jpl,
                'nilai' => $rataRata,
                'ambang_batas' => (float) $materi->ambang_batas,
                'lengkap' => $lengkap,
                'lulus' => $lulus,
                'fasilitator' => optional($efm->fasilitator)->nama_fasilitator,
            ];
        })->values();
    }

    private function nomorSertifikat(Event $event, Registrasi $registrasi)
    {
        $urutan = Registrasi::where('event_id', $event->id)
            ->where('status_pendaftaran', 'Diterima')
            ->orderBy('nama')
            ->pluck('id')
            ->search($registrasi->id);

        $urutan = ($urutan === false ? 0 : $urutan) + 1;
        $noUrut = str_pad($urutan, 3, '0', STR_PAD_LEFT);

        if (!empty($event->nomor_sertifikat)) {
            return $event->nomor_sertifikat . '.' . $noUrut;
        }

        return 'SERT/' . $event->id . '/' . $noUrut . '/' . $event->tahun;
    }

    public function cetak($uuid, $registrasiUuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $event->load('pelatihan');
        $registrasi = Registrasi::where('uuid', $registrasiUuid)
            ->where('event_id', $event->id)
            ->firstOrFail();

        $rincianMateri = $this->susunRincianMateri($event, $registrasi);
        $totalJpl = $rincianMateri->sum('jpl');
        $totalTeori = $rincianMateri->sum('nilai_teori');
        $totalPraktik = $rincianMateri->sum('nilai_praktik');
        $rataRataAkhir = $rincianMateri->whereNotNull('nilai')->isNotEmpty()
            ? round($rincianMateri->whereNotNull('nilai')->avg('nilai'), 2)
            : null;
        $statusKelulusan = $rincianMateri->isNotEmpty() && $rincianMateri->every(fn($m) => $m['lulus'] === true)
            ? 'LULUS'
            : 'BELUM LULUS';

        $nomorSertifikat = $this->nomorSertifikat($event, $registrasi);
        $urlValidasi = route('sertifikat.validasi', [$event->uuid, $registrasi->uuid]);

        return view('sertifikat.cetak', compact(
            'event', 'registrasi', 'rincianMateri', 'totalJpl', 'totalTeori', 'totalPraktik',
            'rataRataAkhir', 'statusKelulusan', 'nomorSertifikat', 'urlValidasi'
        ));
    }

    // Halaman publik hasil scan QR di sertifikat: menampilkan identitas
    // peserta, identitas pelatihan, dan siapa yang menandatangani (direktur).
    public function validasi($uuid, $registrasiUuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $event->load('pelatihan');
        $registrasi = Registrasi::where('uuid', $registrasiUuid)
            ->where('event_id', $event->id)
            ->firstOrFail();

        $nomorSertifikat = $this->nomorSertifikat($event, $registrasi);

        return view('sertifikat.validasi', compact('event', 'registrasi', 'nomorSertifikat'));
    }
}