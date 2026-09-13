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

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function evaluasiPelatihanJawabans()
    {
        return $this->hasMany(EvaluasiPelatihanJawaban::class);
    }

    public function evaluasiFasilitatorJawabans()
    {
        return $this->hasMany(EvaluasiFasilitatorJawaban::class);
    }

    public function evaluasiMateriJawabans()
    {
        return $this->hasMany(EvaluasiMateriJawaban::class);
    }

    // Nilai kompetensi (skill) peserta ini yang diisi oleh fasilitator
    public function penilaianSkillJawabans()
    {
        return $this->hasMany(PenilaianSkillJawaban::class);
    }

    public function getNamaLengkapAttribute()
    {
        $depan = $this->gelar_depan ? $this->gelar_depan . ' ' : '';
        $belakang = $this->gelar_belakang ? ', ' . $this->gelar_belakang : '';
        return $depan . $this->nama . $belakang;
    }

    public function getKekuranganAttribute()
    {
        $biaya = optional($this->event)->biaya_pelatihan ?? 0;
        return max($biaya - $this->total_dibayar, 0);
    }

    public function getLinkPembayaranAttribute()
    {
        return route('pembayaran.public', $this->uuid);
    }

    // Link chat WhatsApp langsung ke nomor peserta
    public function getLinkWhatsappAttribute()
    {
        return $this->no_whatsapp ? 'https://wa.me/' . $this->no_whatsapp : null;
    }
}