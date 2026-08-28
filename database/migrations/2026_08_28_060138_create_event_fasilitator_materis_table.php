<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventFasilitatorMaterisTable extends Migration
{
   public function up()
    {
        Schema::create('event_fasilitator_materis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('fasilitator_id')->constrained('fasilitators')->onDelete('cascade');
            $table->foreignId('evaluasi_materi_id')->constrained('evaluasi_materis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_fasilitator_materis');
    }
}
