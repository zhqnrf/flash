<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifikasisTable extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('tipe'); // pendaftaran_baru, pembayaran_masuk, ikm_masuk, pengaduan_baru
            $table->string('judul');
            $table->text('pesan');
            $table->string('link')->nullable();
            $table->nullableMorphs('notifiable'); // referensi ke Registrasi/Pengaduan/SurveyKepuasan
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['is_read', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
}
