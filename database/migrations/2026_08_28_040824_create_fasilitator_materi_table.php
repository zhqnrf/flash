<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFasilitatorMateriTable extends Migration
{
   public function up()
    {
        Schema::create('fasilitator_materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasilitator_id')->constrained('fasilitators')->onDelete('cascade');
            $table->foreignId('evaluasi_materi_id')->constrained('evaluasi_materis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fasilitator_materi');
    }
}
