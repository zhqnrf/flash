<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenilaianSkillJawabansTable extends Migration
{
    /**
     * Tabel ini menyimpan NILAI PESERTA yang diisi oleh FASILITATOR,
     * per skill (indikator) di dalam sebuah materi (rubrik dari master
     * skill_materis). Ini kebalikan dari evaluasi_materi_jawabans yang
     * berisi peserta menilai kualitas penyampaian materi/fasilitator.
     */
    public function up()
    {
        Schema::create('penilaian_skill_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasis')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('fasilitator_id')->constrained('fasilitators')->onDelete('cascade');
            $table->foreignId('evaluasi_materi_id')->constrained('evaluasi_materis')->onDelete('cascade');
            $table->foreignId('skill_materi_id')->constrained('skill_materis')->onDelete('cascade');
            $table->unsignedInteger('nilai');
            $table->timestamps();

            $table->unique(['registrasi_id', 'skill_materi_id', 'event_id'], 'unik_penilaian_skill');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penilaian_skill_jawabans');
    }
}