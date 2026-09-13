<?php

namespace App\Observers;

use App\Models\Notifikasi;
use App\Models\Registrasi;

class RegistrasiObserver
{
    // Ada peserta baru mendaftar (form publik)
    public function created(Registrasi $registrasi)
    {
        $namaEvent = optional($registrasi->event)->nama_event ?? 'event';

        Notifikasi::catat(
            'pendaftaran_baru',
            'Pendaftaran Baru',
            "{$registrasi->nama} baru saja mendaftar pada {$namaEvent}.",
            $registrasi->event_id ? route('event.pembayaran', $registrasi->event_id) : null,
            $registrasi
        );
    }

    // Ada peserta mengirim bukti pembayaran (pelunasan cicilan via link publik)
    public function updated(Registrasi $registrasi)
    {
        if ($registrasi->isDirty('bukti_bayar_terakhir') && !empty($registrasi->bukti_bayar_terakhir)) {
            $namaEvent = optional($registrasi->event)->nama_event ?? 'event';

            Notifikasi::catat(
                'pembayaran_masuk',
                'Pembayaran Masuk',
                "{$registrasi->nama} mengirim bukti pembayaran untuk {$namaEvent}. Perlu diverifikasi.",
                $registrasi->event_id ? route('event.pembayaran', $registrasi->event_id) : null,
                $registrasi
            );
        }
    }
}
