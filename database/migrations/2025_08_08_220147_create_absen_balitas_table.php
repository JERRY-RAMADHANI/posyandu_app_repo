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
        Schema::create('absen_balitas', function (Blueprint $table) {
            $table->id();
            $table->string('no_reg')->nullable();
            $table->date('tanggal_absen')->nullable();
            $table->string('nik')->nullable();
            $table->string('nama')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->string('alamat')->nullable();
            $table->float('bb')->nullable();
            $table->float('tb')->nullable();
            $table->float('lk')->nullable();
            $table->float('ll')->nullable();
            $table->string('ket')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absen_balitas');
    }
};
