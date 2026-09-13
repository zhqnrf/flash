<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SurveyUnsur;

class SurveyUnsurSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kode' => 'U1',
                'nama_unsur' => 'Persyaratan pelayanan',
                'pertanyaan' => 'Bagaimana pendapat Saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya',
                'opsi_jawaban' => ['Tidak Sesuai', 'Kurang Sesuai', 'Sesuai', 'Sangat Sesuai'],
                'urutan' => 1,
            ],
            [
                'kode' => 'U2',
                'nama_unsur' => 'Prosedur pelayanan',
                'pertanyaan' => 'Bagaimana pemahaman Saudara tentang kemudahan prosedur pelayanan di lembaga ini.',
                'opsi_jawaban' => ['Tidak Mudah', 'Kurang Mudah', 'Mudah', 'Sangat Mudah'],
                'urutan' => 2,
            ],
            [
                'kode' => 'U3',
                'nama_unsur' => 'Waktu pelayanan',
                'pertanyaan' => 'Bagaimana pendapat Saudara tentang Kecepatan waktu dalam Pelayanan.',
                'opsi_jawaban' => ['Tidak Tepat', 'Kurang Tepat', 'Tepat', 'Sangat Tepat'],
                'urutan' => 3,
            ],
            [
                'kode' => 'U4',
                'nama_unsur' => 'Biaya / tarif pelayanan',
                'pertanyaan' => 'Bagaimana pendapat Saudara tentang kewajaran biaya/tarif dalam pelayanan',
                'opsi_jawaban' => ['Tidak Mahal', 'Kurang Mahal', 'Mahal', 'Sangat Mahal'],
                'urutan' => 4,
            ],
            [
                'kode' => 'U5',
                'nama_unsur' => 'Produk Pelayanan',
                'pertanyaan' => 'Bagaimana pendapat Saudara tentang kesesuaian progam pelayanan antara yang tercantum dalam standart pelayanan dengan yang diberikan.',
                'opsi_jawaban' => ['Tidak Sesuai', 'Kurang Sesuai', 'Sesuai', 'Sangat Sesuai'],
                'urutan' => 5,
            ],
            [
                'kode' => 'U6',
                'nama_unsur' => 'Kompetensi petugas pelayanan',
                'pertanyaan' => 'Bagaimana pendapat Saudara tentang kompetensi/kemampuan petugas dalam pelayanan.',
                'opsi_jawaban' => ['Tidak Kompeten', 'Kurang Kompeten', 'Kompeten', 'Sangat Kompeten'],
                'urutan' => 6,
            ],
            [
                'kode' => 'U7',
                'nama_unsur' => 'Perilaku petugas pelayanan',
                'pertanyaan' => 'Bagamana pendapat saudara tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan',
                'opsi_jawaban' => ['Tidak Sopan dan Ramah', 'Kurang Sopan dan Ramah', 'Sopan dan Ramah', 'Sangat Sopan dan Ramah'],
                'urutan' => 7,
            ],
            [
                'kode' => 'U8',
                'nama_unsur' => 'Sarana dan prasarana',
                'pertanyaan' => 'Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana',
                'opsi_jawaban' => ['Buruk', 'Cukup', 'Baik', 'Sangat Baik'],
                'urutan' => 8,
            ],
            [
                'kode' => 'U9',
                'nama_unsur' => 'Penanganan pengaduan layanan',
                'pertanyaan' => 'Bagaimana pendapat Saudara tentang penanganan pengaduan pengguna layanan',
                'opsi_jawaban' => ['Tidak Ada', 'Ada tetapi Tidak Berfungsi', 'Berfungsi Kurang Maksimal', 'Dikelola dengan Baik'],
                'urutan' => 9,
            ],
        ];

        foreach ($data as $item) {
            SurveyUnsur::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}