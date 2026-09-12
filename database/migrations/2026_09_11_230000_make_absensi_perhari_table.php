<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeAbsensiPerHariTable extends Migration
{
    public function up()
    {
        // 1. Tambah kolom sebagai NULLABLE dulu, supaya data lama yang sudah ada tidak bikin error
        Schema::table('absensis', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('registrasi_id');
        });

        // 2. Backfill data lama: ambil tanggal dari jam_masuk yang sudah tercatat
        DB::table('absensis')->whereNull('tanggal')->update([
            'tanggal' => DB::raw('DATE(jam_masuk)'),
        ]);

        // 3. Buat unique index BARU dulu (registrasi_id + tanggal) SEBELUM index lama dihapus,
        //    supaya foreign key registrasi_id tetap punya index pendukung sepanjang proses.
        Schema::table('absensis', function (Blueprint $table) {
            $table->unique(['registrasi_id', 'tanggal'], 'unik_absen_harian');
        });

        // 4. Baru sekarang aman untuk hapus unique index lama (registrasi_id saja)
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('absensis_registrasi_id_unique');
        });
    }

    public function down()
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->unique('registrasi_id', 'absensis_registrasi_id_unique');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('unik_absen_harian');
            $table->dropColumn('tanggal');
        });
    }
}