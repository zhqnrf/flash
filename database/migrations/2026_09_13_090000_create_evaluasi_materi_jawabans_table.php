<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluasiMateriJawabansTable extends Migration
{
    public function up()
    {
        Schema::create('evaluasi_materi_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasis')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('fasilitator_id')->constrained('fasilitators')->onDelete('cascade');
            $table->foreignId('evaluasi_materi_id')->constrained('evaluasi_materis')->onDelete('cascade');
            $table->unsignedInteger('nilai');
            $table->text('saran')->nullable();
            $table->timestamps();

            $table->unique(['registrasi_id', 'evaluasi_materi_id', 'fasilitator_id'], 'unik_jawaban_materi');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluasi_materi_jawabans');
    }
}