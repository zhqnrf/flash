<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyKepuasanJawaban extends Model
{
    protected $guarded = ['id'];

    public function surveyKepuasan()
    {
        return $this->belongsTo(SurveyKepuasan::class);
    }

    public function surveyUnsur()
    {
        return $this->belongsTo(SurveyUnsur::class);
    }
}