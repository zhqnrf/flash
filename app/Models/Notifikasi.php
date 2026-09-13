<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    // Ikon per tipe notifikasi (dipakai di dropdown topbar)
    public function getIkonAttribute()
    {
        switch ($this->tipe) {
            case 'pendaftaran_baru':
                return 'user-plus';
            case 'pembayaran_masuk':
                return 'currency';
            case 'ikm_masuk':
                return 'clipboard';
            case 'pengaduan_baru':
                return 'alert';
            default:
                return 'bell';
        }
    }

    public function scopeBelumDibaca($query)
    {
        return $query->where('is_read', false);
    }

    public static function catat(string $tipe, string $judul, string $pesan, ?string $link = null, $notifiable = null): self
    {
        return static::create([
            'tipe' => $tipe,
            'judul' => $judul,
            'pesan' => $pesan,
            'link' => $link,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable ? $notifiable->id : null,
        ]);
    }
}