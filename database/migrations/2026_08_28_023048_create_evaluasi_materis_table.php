<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluasiMaterisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::create('evaluasi_materis', function (Blueprint $table) {
        $table->id();
        // Relasi ke tabel pelatihans
        $table->foreignId('pelatihan_id')->constrained('pelatihans')->onDelete('cascade');
        
        $table->string('nama_materi');
        $table->json('unsur'); // Menyimpan array [U1, U2, dll]
        $table->decimal('ambang_batas', 5, 2);
        $table->string('tema'); // Indikator dll (Dropdown)
        
        $table->integer('nilai_teori');
        $table->integer('nilai_praktik');
        $table->integer('jpl'); // Akumulasi (Teori + Praktik)
        
        // Rentang nilai
        $table->integer('rentang_nilai_min');
        $table->integer('rentang_nilai_max');
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluasi_materis');
    }
}
