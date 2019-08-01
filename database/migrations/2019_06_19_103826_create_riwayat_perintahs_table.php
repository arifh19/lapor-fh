<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiwayatPerintahsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riwayat_perintahs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('perintah_id')->references('id')->on('perintahs')->onDelete('cascade');
            $table->unsignedInteger('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->unsignedInteger('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('laporan');
            $table->string('dokumentasi')->nullable(); 
            $table->timestamps();

            $table->foreign('perintah_id')->references('id')->on('perintahs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riwayat_perintahs');
    }
}
