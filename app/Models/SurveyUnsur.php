<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyUnsur extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['opsi_jawaban' => 'array'];

    public function jawabans()
    {
        return $this->hasMany(SurveyKepuasanJawaban::class);
    }
}