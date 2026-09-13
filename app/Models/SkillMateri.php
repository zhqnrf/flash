<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillMateri extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
public function skills()
{
    return $this->hasMany(SkillMateri::class, 'evaluasi_materi_id');
}
    public function evaluasiMateri()
    {
        return $this->belongsTo(EvaluasiMateri::class, 'evaluasi_materi_id');
    }

    public function penilaianSkillJawabans()
    {
        return $this->hasMany(PenilaianSkillJawaban::class);
    }
}