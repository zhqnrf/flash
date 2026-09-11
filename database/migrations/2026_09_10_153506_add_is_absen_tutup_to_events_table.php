<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAbsenTutupToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('events', function (Blueprint $table) {
        $table->boolean('is_absen_tutup')->default(false)->after('waktu_presensi_selesai');
    });
}
public function down()
{
    Schema::table('events', function (Blueprint $table) {
        $table->dropColumn('is_absen_tutup');
    });
}
}
