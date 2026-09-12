<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'jam_masuk' => 'datetime',
        'tanggal' => 'date',
    ];

    public function registrasi()
    {
        return $this->belongsTo(Registrasi::class);
    }
}