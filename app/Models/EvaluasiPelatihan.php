<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiPelatihan extends Model
{
    use HasFactory;
    protected $fillable = ['pelatihan_id', 'jenis_evaluasi', 'nama_evaluasi', 'rentang_nilai_min', 'rentang_nilai_max'];

public function pelatihan() {
    return $this->belongsTo(Pelatihan::class)->withDefault([
        'nama_pelatihan' => 'Semua Pelatihan (Global)'
    ]);
}
    
}