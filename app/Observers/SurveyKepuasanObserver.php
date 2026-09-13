<?php

namespace App\Observers;

use App\Models\Notifikasi;
use App\Models\SurveyKepuasan;

class SurveyKepuasanObserver
{
    public function created(SurveyKepuasan $survey)
    {
        Notifikasi::catat(
            'ikm_masuk',
            'Survey IKM Baru',
            "Ada pengisian survey IKM baru ({$survey->pekerjaan}, usia {$survey->usia}).",
            route('survey-kepuasan.rekap'),
            $survey
        );
    }
}
