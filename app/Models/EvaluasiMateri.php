<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiMateri extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'pelatihan_id', 'nama_materi', 'unsur', 'ambang_batas', 
        'tema', 'nilai_teori', 'nilai_praktik', 'jpl', 
        'rentang_nilai_min', 'rentang_nilai_max'
    ];

    // Otomatis ubah JSON di database jadi Array saat ditarik ke Laravel
    protected $casts = [
        'unsur' => 'array',
    ];
public function skills()
{
    return $this->hasMany(SkillMateri::class, 'evaluasi_materi_id');
}
    public function pelatihan() {
        return $this->belongsTo(Pelatihan::class);
    }
}