<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFasilitatorsTable extends Migration
{
   public function up()
    {
        Schema::create('fasilitators', function (Blueprint $table) {
            $table->id();
            $table->string('nama_fasilitator');
            $table->string('profesi_fasilitator')->nullable();
            $table->string('jabatan_fasilitator')->nullable();
            $table->string('tempat_kerja_fasilitator')->nullable();
            $table->string('pendidikan_terakhir_fasilitator')->nullable();
            $table->string('dokumen_fasilitator')->nullable(); // Path file dokumen (PDF/Word)
            $table->string('foto_fasilitator')->nullable();   // Path foto 3x4
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fasilitators');
    }
}
