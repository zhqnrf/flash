<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasaBerlakuSertifikatToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->date('sertifikat_berlaku_mulai')->nullable()->after('status_absen');
            $table->date('sertifikat_berlaku_selesai')->nullable()->after('sertifikat_berlaku_mulai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['sertifikat_berlaku_mulai', 'sertifikat_berlaku_selesai']);
        });
    }
}