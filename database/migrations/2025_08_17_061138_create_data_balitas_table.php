<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_balitas', function (Blueprint $table) {
            $table->id();
            $table->string('no_reg')->unique()->nullable();
            $table->string('prov')->nullable();
            $table->string('kab')->nullable();
            $table->string('kec')->nullable();
            $table->string('nik_2_dig')->nullable();
            $table->integer('anak_ke')->nullable();
            $table->float('bb_lahir')->nullable();
            $table->float('panjang_lahir')->nullable();
            $table->integer('buku_kia')->nullable();
            $table->string('nama')->nullable();
            $table->string('nik')->unique()->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('tgl')->nullable();
            $table->integer('bln')->nullable();
            $table->integer('thn')->nullable();
            $table->integer('usia')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('posyandu')->nullable();
            $table->string('nama_ortu')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_balitas');
    }
};
