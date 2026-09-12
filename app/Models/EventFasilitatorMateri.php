<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventFasilitatorMateri extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_sesi' => 'date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function fasilitator()
    {
        return $this->belongsTo(Fasilitator::class, 'fasilitator_id');
    }

    public function evaluasiMateri()
    {
        return $this->belongsTo(EvaluasiMateri::class, 'evaluasi_materi_id');
    }
}