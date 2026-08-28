<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
   public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // Untuk keamanan Link Publik
            
            // Info Dasar
            $table->string('nama_event');
            $table->string('batch')->nullable();
            $table->string('tahun');
            $table->foreignId('pelatihan_id')->constrained('pelatihans')->onDelete('cascade');
            
            // Tipe & Sistem
            $table->enum('tipe_pelatihan', ['Workshop', 'Webinar', 'Pelatihan', 'Seminar']);
            $table->enum('sistem_pelatihan', ['Daring', 'Luring', 'Blended']);
            $table->string('link_zoom')->nullable(); // Bisa null, diisi jika Daring/Blended
            $table->enum('jenis_pelatihan', ['Kerjasama', 'Mandiri']);
            
            // Penyelenggara & Detail
            $table->text('instansi_penyelenggara')->nullable(); // Bisa banyak instansi (string/JSON)
            $table->integer('skp')->default(0);
            $table->string('banner')->nullable(); // Max 2MB divalidasi nanti
            
            // Waktu & Tempat
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('waktu_presensi_mulai');
            $table->time('waktu_presensi_selesai');
            $table->string('lokasi')->nullable();
            
            // Komponen Tambahan
            $table->string('link_materi')->nullable(); // Link Drive
            $table->boolean('has_presensi')->default(true); // Ada presensi atau tidak
            
            // Sertifikat & Pembayaran
            $table->string('warna_sertifikat')->nullable();
            $table->string('nomor_sertifikat')->nullable();
            $table->text('rekening_pembayaran')->nullable();
            $table->integer('biaya_pelatihan')->default(0);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}
