<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluasiPelatihanJawabansTable extends Migration
{
    public function up()
    {
        Schema::create('evaluasi_pelatihan_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasis')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('evaluasi_pelatihan_id')->constrained('evaluasi_pelatihans')->onDelete('cascade');
            $table->unsignedInteger('nilai');
            $table->timestamps();

            $table->unique(['registrasi_id', 'evaluasi_pelatihan_id'], 'unik_jawaban_pelatihan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluasi_pelatihan_jawabans');
    }
}