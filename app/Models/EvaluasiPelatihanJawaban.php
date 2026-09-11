<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiPelatihanJawaban extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function registrasi()
    {
        return $this->belongsTo(Registrasi::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function evaluasiPelatihan()
    {
        return $this->belongsTo(EvaluasiPelatihan::class);
    }
}