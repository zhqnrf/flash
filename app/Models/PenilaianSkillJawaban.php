<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianSkillJawaban extends Model
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

    public function fasilitator()
    {
        return $this->belongsTo(Fasilitator::class);
    }

    public function evaluasiMateri()
    {
        return $this->belongsTo(EvaluasiMateri::class, 'evaluasi_materi_id');
    }

    public function skillMateri()
    {
        return $this->belongsTo(SkillMateri::class, 'skill_materi_id');
    }
}