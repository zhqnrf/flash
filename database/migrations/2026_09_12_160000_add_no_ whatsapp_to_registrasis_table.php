<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoWhatsappToRegistrasisTable extends Migration
{
    public function up()
    {
        Schema::table('registrasis', function (Blueprint $table) {
            $table->string('no_whatsapp', 20)->nullable()->after('email_plataran_sehat');
        });
    }

    public function down()
    {
        Schema::table('registrasis', function (Blueprint $table) {
            $table->dropColumn('no_whatsapp');
        });
    }
}