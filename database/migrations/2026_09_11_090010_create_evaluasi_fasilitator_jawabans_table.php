<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluasiFasilitatorJawabansTable extends Migration
{
    public function up()
    {
        Schema::create('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasis')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('fasilitator_id')->constrained('fasilitators')->onDelete('cascade');
            $table->foreignId('evaluasi_fasilitator_id')->constrained('evaluasi_fasilitators')->onDelete('cascade');
            $table->unsignedInteger('nilai');
            $table->timestamps();

            $table->unique(['registrasi_id', 'fasilitator_id', 'evaluasi_fasilitator_id'], 'unik_jawaban_fasilitator');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluasi_fasilitator_jawabans');
    }
}