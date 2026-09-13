<?php

namespace App\Observers;

use App\Models\Notifikasi;
use App\Models\Pengaduan;

class PengaduanObserver
{
    public function created(Pengaduan $pengaduan)
    {
        Notifikasi::catat(
            'pengaduan_baru',
            'Pengaduan Baru',
            "{$pengaduan->nama_lengkap} mengirim pengaduan baru: \"" . \Illuminate\Support\Str::limit($pengaduan->isi_pengaduan, 60) . '"',
            route('pengaduan.index'),
            $pengaduan
        );
    }
}
