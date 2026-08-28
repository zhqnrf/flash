<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitator extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Evaluasi Materi (Many to Many)
    public function materis()
    {
        return $this->belongsToMany(EvaluasiMateri::class, 'fasilitator_materi', 'fasilitator_id', 'evaluasi_materi_id');
    }
}