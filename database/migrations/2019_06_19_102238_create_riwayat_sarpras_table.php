<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiwayatSarprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riwayat_sarpras', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sarpras_id');
            $table->unsignedInteger('unit_id');
            $table->unsignedInteger('user_id');
            $table->string('laporan');
            $table->string('dokumentasi')->nullable();            
            $table->timestamps();
            $table->foreign('unit_id')->references('id')->on('units')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sarpras_id')->references('id')->on('sarpras')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('riwayat_sarpras');
    }
}
