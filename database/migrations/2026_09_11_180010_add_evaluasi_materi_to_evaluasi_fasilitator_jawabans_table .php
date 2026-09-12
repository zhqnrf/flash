<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvaluasiMateriToEvaluasiFasilitatorJawabansTable extends Migration
{
    public function up()
    {
        // 1. Tambah kolom evaluasi_materi_id dulu (nullable + FK)
        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->foreignId('evaluasi_materi_id')->nullable()->after('fasilitator_id')->constrained('evaluasi_materis')->onDelete('cascade');
        });

        // 2. Buat unique index BARU dulu SEBELUM yang lama dihapus,
        //    supaya foreign key registrasi_id/fasilitator_id tetap punya index pendukung.
        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->unique(['registrasi_id', 'fasilitator_id', 'evaluasi_materi_id', 'evaluasi_fasilitator_id'], 'unik_jawaban_fasilitator_materi');
        });

        // 3. Baru sekarang aman untuk hapus unique index lama
        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->dropUnique('unik_jawaban_fasilitator');
        });
    }

    public function down()
    {
        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->unique(['registrasi_id', 'fasilitator_id', 'evaluasi_fasilitator_id'], 'unik_jawaban_fasilitator');
        });

        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->dropUnique('unik_jawaban_fasilitator_materi');
            $table->dropConstrainedForeignId('evaluasi_materi_id');
        });
    }
}