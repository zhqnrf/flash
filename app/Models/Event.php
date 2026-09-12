<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Auto generate UUID saat membuat event baru
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relasi ke Master Pelatihan
    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id');
    }

    // Relasi ke Pivot Pemetaan Fasilitator & Materi
    public function eventFasilitatorMateris()
    {
        return $this->hasMany(EventFasilitatorMateri::class, 'event_id');
    }
}