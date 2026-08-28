<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluasiFasilitatorsTable extends Migration
{
public function up()
{
    Schema::create('evaluasi_fasilitators', function (Blueprint $table) {
        $table->id();
        // Hapus atau jadikan nullable
        $table->foreignId('pelatihan_id')->nullable()->constrained('pelatihans')->nullOnDelete();
        $table->string('jenis_evaluasi');
        $table->string('nama_evaluasi');
        $table->integer('rentang_nilai_min');
        $table->integer('rentang_nilai_max');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluasi_fasilitators');
    }
}
