<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RevertEvaluasiMateriFromEvaluasiFasilitatorJawabansTable extends Migration
{
    public function up()
    {
        // 1. Buat unique index versi LAMA (3 kolom) dulu SEBELUM yang baru dihapus,
        //    supaya foreign key registrasi_id/fasilitator_id tetap ada index pendukung.
        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->unique(['registrasi_id', 'fasilitator_id', 'evaluasi_fasilitator_id'], 'unik_jawaban_fasilitator');
        });

        // 2. Baru sekarang aman hapus unique index yang menyertakan evaluasi_materi_id
        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->dropUnique('unik_jawaban_fasilitator_materi');
        });

        // 3. Hapus kolom evaluasi_materi_id beserta foreign key-nya (sudah tidak dipakai di tabel ini)
        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('evaluasi_materi_id');
        });
    }

    public function down()
    {
        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->foreignId('evaluasi_materi_id')->nullable()->after('fasilitator_id')->constrained('evaluasi_materis')->onDelete('cascade');
        });

        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->unique(['registrasi_id', 'fasilitator_id', 'evaluasi_materi_id', 'evaluasi_fasilitator_id'], 'unik_jawaban_fasilitator_materi');
        });

        Schema::table('evaluasi_fasilitator_jawabans', function (Blueprint $table) {
            $table->dropUnique('unik_jawaban_fasilitator');
        });
    }
}