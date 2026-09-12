<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTanggalSesiToEventFasilitatorMaterisTable extends Migration
{
    public function up()
    {
        Schema::table('event_fasilitator_materis', function (Blueprint $table) {
            $table->date('tanggal_sesi')->nullable()->after('evaluasi_materi_id');
        });
    }

    public function down()
    {
        Schema::table('event_fasilitator_materis', function (Blueprint $table) {
            $table->dropColumn('tanggal_sesi');
        });
    }
}