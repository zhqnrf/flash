<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaranToEvaluasiJawabansTables extends Migration
{
    public function up()
    {
        Schema::table('evaluasi_pelatihan_jawabans', function (Blueprint $table) {
            $table->text('saran')->nullable()->after('nilai');
        });

        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->text('saran')->nullable()->after('nilai');
        });
    }

    public function down()
    {
        Schema::table('evaluasi_pelatihan_jawabans', function (Blueprint $table) {
            $table->dropColumn('saran');
        });

        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->dropColumn('saran');
        });
    }
}