<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SurveyKepuasan extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['tanggal_survey' => 'date'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function jawabans()
    {
        return $this->hasMany(SurveyKepuasanJawaban::class);
    }
}