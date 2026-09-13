<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyKepuasanTables extends Migration
{
    public function up()
    {
        // Master 9 Unsur Pelayanan (U1-U9) - isi statis lewat seeder
        Schema::create('survey_unsurs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 5); // U1, U2, ... U9
            $table->string('nama_unsur');
            $table->text('pertanyaan');
            $table->json('opsi_jawaban'); // 4 opsi jawaban kualitatif, index 0 = nilai 1, dst
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        // Data responden per pengisian survey
        Schema::create('survey_kepuasans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->date('tanggal_survey');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->unsignedInteger('usia');
            $table->string('pendidikan_terakhir'); // SD/SMP/SMA/Diploma/Sarjana/Lainnya
            $table->string('pendidikan_lainnya')->nullable(); // isian bebas jika pilih "Yang lain"
            $table->string('pekerjaan');
            $table->timestamps();
        });

        // Jawaban per unsur (U1-U9) untuk tiap pengisian survey
        Schema::create('survey_kepuasan_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_kepuasan_id')->constrained('survey_kepuasans')->onDelete('cascade');
            $table->foreignId('survey_unsur_id')->constrained('survey_unsurs')->onDelete('cascade');
            $table->unsignedTinyInteger('nilai'); // 1-4
            $table->timestamps();

            $table->unique(['survey_kepuasan_id', 'survey_unsur_id'], 'unik_jawaban_survey_unsur');
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_kepuasan_jawabans');
        Schema::dropIfExists('survey_kepuasans');
        Schema::dropIfExists('survey_unsurs');
    }
}