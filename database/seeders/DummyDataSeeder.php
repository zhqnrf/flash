<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Pelatihan;
use App\Models\EvaluasiMateri;
use App\Models\EvaluasiPelatihan;
use App\Models\EvaluasiFasilitator;
use App\Models\Fasilitator;
use App\Models\SkillMateri;
use App\Models\Event;
use App\Models\EventFasilitatorMateri;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // ==========================================
        // 1. MASTER PELATIHAN
        // ==========================================
        $pelatihans = [
            Pelatihan::create(['nomor' => 'PLT-001', 'nama_pelatihan' => 'Pelatihan Bantuan Hidup Dasar (BHD)']),
            Pelatihan::create(['nomor' => 'PLT-002', 'nama_pelatihan' => 'Pencegahan dan Pengendalian Infeksi (PPI)']),
            Pelatihan::create(['nomor' => 'PLT-003', 'nama_pelatihan' => 'Keselamatan & Kesehatan Kerja RS (K3RS)']),
            Pelatihan::create(['nomor' => 'PLT-004', 'nama_pelatihan' => 'Peningkatan Mutu & Keselamatan Pasien (PMKP)']),
            Pelatihan::create(['nomor' => 'PLT-005', 'nama_pelatihan' => 'Pelayanan Obstetri Neonatal Emergensi (PONEK)']),
        ];

        // ==========================================
        // 2. MASTER EVALUASI MATERI (Sesuai Model: Unsur Array & Tambah JPL)
        // ==========================================
        $materis = [
            EvaluasiMateri::create(['pelatihan_id' => $pelatihans[0]->id, 'unsur' => ['Materi Inti'], 'tema' => 'Klinis', 'nama_materi' => 'Resusitasi Jantung Paru (RJP)', 'ambang_batas' => 80, 'nilai_teori' => 80, 'nilai_praktik' => 80, 'jpl' => 2, 'rentang_nilai_min' => 1, 'rentang_nilai_max' => 4]),
            EvaluasiMateri::create(['pelatihan_id' => $pelatihans[0]->id, 'unsur' => ['Materi Penunjang'], 'tema' => 'Klinis', 'nama_materi' => 'Penggunaan AED', 'ambang_batas' => 80, 'nilai_teori' => 80, 'nilai_praktik' => 80, 'jpl' => 1, 'rentang_nilai_min' => 1, 'rentang_nilai_max' => 4]),
            EvaluasiMateri::create(['pelatihan_id' => $pelatihans[1]->id, 'unsur' => ['Materi Inti'], 'tema' => 'Manajemen Fasilitas', 'nama_materi' => 'Hand Hygiene & Penggunaan APD', 'ambang_batas' => 75, 'nilai_teori' => 75, 'nilai_praktik' => 75, 'jpl' => 2, 'rentang_nilai_min' => 1, 'rentang_nilai_max' => 4]),
            EvaluasiMateri::create(['pelatihan_id' => $pelatihans[2]->id, 'unsur' => ['Materi Inti'], 'tema' => 'K3', 'nama_materi' => 'Simulasi Code Red & APAR', 'ambang_batas' => 70, 'nilai_teori' => 70, 'nilai_praktik' => 70, 'jpl' => 3, 'rentang_nilai_min' => 1, 'rentang_nilai_max' => 5]),
            EvaluasiMateri::create(['pelatihan_id' => $pelatihans[3]->id, 'unsur' => ['Materi Dasar'], 'tema' => 'Mutu', 'nama_materi' => 'Pelaporan Insiden Keselamatan Pasien', 'ambang_batas' => 85, 'nilai_teori' => 85, 'nilai_praktik' => 85, 'jpl' => 2, 'rentang_nilai_min' => 1, 'rentang_nilai_max' => 100]),
            EvaluasiMateri::create(['pelatihan_id' => $pelatihans[4]->id, 'unsur' => ['Materi Inti'], 'tema' => 'Klinis Khusus', 'nama_materi' => 'Resusitasi Neonatus Dasar', 'ambang_batas' => 80, 'nilai_teori' => 80, 'nilai_praktik' => 80, 'jpl' => 4, 'rentang_nilai_min' => 1, 'rentang_nilai_max' => 4]),
        ];

        // ==========================================
        // 3. MASTER FASILITATOR & RELASINYA
        // ==========================================
        $fasilitators = [
            Fasilitator::create(['nama_fasilitator' => 'Dr. H. Ahmad Fauzi, Sp.An', 'profesi_fasilitator' => 'Dokter Spesialis Anestesi', 'jabatan_fasilitator' => 'Ketua Tim Code Blue']),
            Fasilitator::create(['nama_fasilitator' => 'Ns. Siti Aminah, S.Kep', 'profesi_fasilitator' => 'Perawat IPCN', 'jabatan_fasilitator' => 'Komite PPI']),
            Fasilitator::create(['nama_fasilitator' => 'Ir. Budi Santoso, M.T', 'profesi_fasilitator' => 'Ahli K3 Umum', 'jabatan_fasilitator' => 'Ketua Tim K3RS']),
            Fasilitator::create(['nama_fasilitator' => 'Dr. Ratna Wulandari, MARS', 'profesi_fasilitator' => 'Manajemen Rumahsakit', 'jabatan_fasilitator' => 'Ketua Komite PMKP']),
            Fasilitator::create(['nama_fasilitator' => 'Dr. Andi Pratama, Sp.OG', 'profesi_fasilitator' => 'Dokter Spesialis Obgyn', 'jabatan_fasilitator' => 'DPJP PONEK']),
        ];

        // Mapping Fasilitator ke Materi
        $fasilitators[0]->materis()->sync([$materis[0]->id, $materis[1]->id]); 
        $fasilitators[1]->materis()->sync([$materis[2]->id]); 
        $fasilitators[2]->materis()->sync([$materis[3]->id]); 
        $fasilitators[3]->materis()->sync([$materis[4]->id]); 
        $fasilitators[4]->materis()->sync([$materis[5]->id]); 

        // ==========================================
        // 4. MASTER SKILL MATERI
        // ==========================================
        $skills = [
            ['evaluasi_materi_id' => $materis[0]->id, 'nama_skill' => 'Ketepatan Kedalaman Kompresi Dada', 'min' => 1, 'max' => 4],
            ['evaluasi_materi_id' => $materis[1]->id, 'nama_skill' => 'Kecepatan Pemasangan Pad Defib', 'min' => 1, 'max' => 4],
            ['evaluasi_materi_id' => $materis[2]->id, 'nama_skill' => 'Urutan 6 Langkah Cuci Tangan WHO', 'min' => 1, 'max' => 4],
            ['evaluasi_materi_id' => $materis[3]->id, 'nama_skill' => 'Cara Mencabut Pin dan Menyapu Api (PASS)', 'min' => 1, 'max' => 5],
            ['evaluasi_materi_id' => $materis[4]->id, 'nama_skill' => 'Pembuatan Laporan RCA & Grading', 'min' => 1, 'max' => 100],
            ['evaluasi_materi_id' => $materis[5]->id, 'nama_skill' => 'Pemasangan Ventilasi Tekanan Positif (VTP)', 'min' => 1, 'max' => 4],
        ];

        foreach ($skills as $skill) {
            SkillMateri::create([
                'evaluasi_materi_id' => $skill['evaluasi_materi_id'],
                'nama_skill' => $skill['nama_skill'],
                'rentang_nilai_min' => $skill['min'],
                'rentang_nilai_max' => $skill['max'],
            ]);
        }

        // ==========================================
        // 5. DATA EVENT PELATIHAN
        // ==========================================
        $eventsData = [
            [
                'pelatihan_id' => $pelatihans[0]->id, 'nama_event' => 'Inhouse Training BHD Gelombang 1', 'tipe' => 'Pelatihan', 'sistem' => 'Luring', 
                'materi_ids' => [$materis[0]->id, $materis[1]->id], 'fasilitator_id' => $fasilitators[0]->id
            ],
            [
                'pelatihan_id' => $pelatihans[1]->id, 'nama_event' => 'Workshop PPI Orientasi Pegawai Baru', 'tipe' => 'Workshop', 'sistem' => 'Blended', 
                'materi_ids' => [$materis[2]->id], 'fasilitator_id' => $fasilitators[1]->id
            ],
            [
                'pelatihan_id' => $pelatihans[2]->id, 'nama_event' => 'Simulasi Kebakaran & APAR', 'tipe' => 'Pelatihan', 'sistem' => 'Luring', 
                'materi_ids' => [$materis[3]->id], 'fasilitator_id' => $fasilitators[2]->id
            ],
            [
                'pelatihan_id' => $pelatihans[3]->id, 'nama_event' => 'Webinar Refreshment PMKP Akreditasi', 'tipe' => 'Webinar', 'sistem' => 'Daring', 
                'materi_ids' => [$materis[4]->id], 'fasilitator_id' => $fasilitators[3]->id
            ],
            [
                'pelatihan_id' => $pelatihans[4]->id, 'nama_event' => 'Pelatihan PONEK Bidan PTT', 'tipe' => 'Pelatihan', 'sistem' => 'Luring', 
                'materi_ids' => [$materis[5]->id], 'fasilitator_id' => $fasilitators[4]->id
            ],
        ];

        foreach ($eventsData as $idx => $ev) {
            $event = Event::create([
                'uuid' => (string) Str::uuid(),
                'nama_event' => $ev['nama_event'],
                'batch' => 'Batch ' . ($idx + 1),
                'tahun' => '2026',
                'pelatihan_id' => $ev['pelatihan_id'],
                'tipe_pelatihan' => $ev['tipe'],
                'sistem_pelatihan' => $ev['sistem'],
                'jenis_pelatihan' => ($idx % 2 == 0) ? 'Mandiri' : 'Kerjasama',
                'instansi_penyelenggara' => 'RSUD Simpang Lima Gumul Kediri',
                'skp' => 2 + $idx,
                'tanggal_mulai' => Carbon::now()->addDays($idx * 2)->toDateString(),
                'tanggal_selesai' => Carbon::now()->addDays(($idx * 2) + 1)->toDateString(),
                'waktu_presensi_mulai' => '07:00:00',
                'waktu_presensi_selesai' => '08:00:00',
                'lokasi' => ($ev['sistem'] !== 'Daring') ? 'Ruang Aula Utama' : null,
                'has_presensi' => true,
                'warna_sertifikat' => '#1a365d',
                'biaya_pelatihan' => ($idx == 3) ? 150000 : 0,
            ]);

            foreach ($ev['materi_ids'] as $mat_id) {
                EventFasilitatorMateri::create([
                    'event_id' => $event->id,
                    'evaluasi_materi_id' => $mat_id,
                    'fasilitator_id' => $ev['fasilitator_id'],
                ]);
            }
        }

        // ==========================================
        // 6. DUMMY EVALUASI PELATIHAN & FASILITATOR
        // ==========================================
        // Agar waktu link evaluasi dibuka, formnya tidak kosong.
        EvaluasiPelatihan::create(['pelatihan_id' => $pelatihans[0]->id, 'jenis_evaluasi' => 'Pilihan Ganda', 'nama_evaluasi' => 'Kenyamanan Ruang Pelatihan', 'rentang_nilai_min' => 1, 'rentang_nilai_max' => 5]);
        EvaluasiPelatihan::create(['pelatihan_id' => $pelatihans[0]->id, 'jenis_evaluasi' => 'Pilihan Ganda', 'nama_evaluasi' => 'Kesesuaian Waktu Pelatihan', 'rentang_nilai_min' => 1, 'rentang_nilai_max' => 5]);

        EvaluasiFasilitator::create(['pelatihan_id' => $pelatihans[0]->id, 'jenis_evaluasi' => 'Pilihan Ganda', 'nama_evaluasi' => 'Penguasaan Materi oleh Fasilitator', 'rentang_nilai_min' => 1, 'rentang_nilai_max' => 5]);
        EvaluasiFasilitator::create(['pelatihan_id' => $pelatihans[0]->id, 'jenis_evaluasi' => 'Pilihan Ganda', 'nama_evaluasi' => 'Kejelasan Suara & Komunikasi', 'rentang_nilai_min' => 1, 'rentang_nilai_max' => 5]);

        $this->command->info('Berhasil! Semua Data Master, Transaksi, dan Kuesioner Evaluasi telah digenerate dengan sempurna!');
    }
}