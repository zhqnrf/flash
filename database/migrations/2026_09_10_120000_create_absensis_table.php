<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
class CreateAbsensisTable extends Migration
{
    public function up()
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->unique()->constrained('registrasis')->onDelete('cascade');
            $table->string('foto_selfie');
            $table->timestamp('jam_masuk');
            $table->timestamps();
        });
    }
 
    public function down()
    {
        Schema::dropIfExists('absensis');
    }
}