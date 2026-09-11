<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UbahSistemStatusAbsenPadaEventsTable extends Migration
{
  public function up()
{
    Schema::table('events', function (Blueprint $table) {
        // Hapus kolom lama
        if (Schema::hasColumn('events', 'is_absen_tutup')) {
            $table->dropColumn('is_absen_tutup');
        }
        // Tambah kolom baru dengan 3 pilihan (otomatis, buka_paksa, tutup_paksa)
        $table->string('status_absen')->default('otomatis')->after('waktu_presensi_selesai');
    });
}

public function down()
{
    Schema::table('events', function (Blueprint $table) {
        $table->dropColumn('status_absen');
        $table->boolean('is_absen_tutup')->default(false);
    });
}
}
