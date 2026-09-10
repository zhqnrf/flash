<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrasisTable extends Migration
{
    public function up()
    {
        Schema::create('registrasis', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); 
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            
            // Data Pribadi
            $table->string('gelar_depan')->nullable();
            $table->string('nama');
            $table->string('gelar_belakang')->nullable();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('nik');
            $table->string('email_plataran_sehat');
            
            // Data Pekerjaan & Instansi
            $table->string('nip')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->string('instansi');
            $table->string('departemen')->nullable();
            $table->text('alamat_lengkap');
            
            // Fasilitas
            $table->string('ukuran_kaos')->nullable(); // Hanya untuk Luring/Blended
            
            // Status & Pembayaran
            $table->enum('status_pendaftaran', ['Menunggu', 'Diterima', 'Ditolak'])->default('Menunggu');
            $table->enum('status_pembayaran', ['Belum Bayar', 'Cicil', 'Lunas'])->default('Belum Bayar');
            $table->integer('total_dibayar')->default(0); 
            $table->string('bukti_bayar_pertama')->nullable(); 
            $table->string('bukti_bayar_terakhir')->nullable(); 
            
            $table->boolean('komitmen')->default(false);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registrasis');
    }
}