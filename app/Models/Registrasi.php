<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Registrasi extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relasi ke Event
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // Relasi ke Absensi (1 peserta bisa punya beberapa absen, 1 per hari pelatihan)
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    // Relasi ke jawaban Evaluasi Pelatihan yang diisi peserta ini
    public function evaluasiPelatihanJawabans()
    {
        return $this->hasMany(EvaluasiPelatihanJawaban::class);
    }

    // Relasi ke jawaban Evaluasi Fasilitator yang diisi peserta ini
    public function evaluasiFasilitatorJawabans()
    {
        return $this->hasMany(EvaluasiFasilitatorJawaban::class);
    }

    // Accessor untuk gabung nama lengkap + gelar otomatis
    public function getNamaLengkapAttribute()
    {
        $depan = $this->gelar_depan ? $this->gelar_depan . ' ' : '';
        $belakang = $this->gelar_belakang ? ', ' . $this->gelar_belakang : '';
        return $depan . $this->nama . $belakang;
    }

    // Sisa kekurangan pembayaran = biaya pelatihan - total yang sudah dibayar
    public function getKekuranganAttribute()
    {
        $biaya = optional($this->event)->biaya_pelatihan ?? 0;
        return max($biaya - $this->total_dibayar, 0);
    }

    // Link publik untuk peserta upload bukti pelunasan cicilan
    public function getLinkPembayaranAttribute()
    {
        return route('pembayaran.public', $this->uuid);
    }
    public function getLinkWhatsappAttribute()
{
    return $this->no_whatsapp ? 'https://wa.me/' . $this->no_whatsapp : null;
}
}