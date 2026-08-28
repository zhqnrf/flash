<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillMaterisTable extends Migration
{
   public function up()
    {
        Schema::create('skill_materis', function (Blueprint $table) {
            $table->id();
            // Relasi ke master evaluasi materi
            $table->foreignId('evaluasi_materi_id')->constrained('evaluasi_materis')->onDelete('cascade');
            $table->string('nama_skill');
            $table->integer('rentang_nilai_min')->default(1);
            $table->integer('rentang_nilai_max')->default(4);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('skill_materis');
    }
}
