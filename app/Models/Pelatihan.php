<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{
    use HasFactory;
    protected $fillable = ['nomor', 'nama_pelatihan'];

    // Relasi One-to-Many
    public function evaluasiMateris() {
        return $this->hasMany(EvaluasiMateri::class);
    }
    public function evaluasiPelatihans() {
        return $this->hasMany(EvaluasiPelatihan::class);
    }
    public function evaluasiFasilitators() {
        return $this->hasMany(EvaluasiFasilitator::class);
    }
}