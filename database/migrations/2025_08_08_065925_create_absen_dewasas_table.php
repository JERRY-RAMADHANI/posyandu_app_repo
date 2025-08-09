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
        Schema::create('absen_dewasas', function (Blueprint $table) {
            $table->id();
            $table->string('no_reg')->nullable();
            $table->date('tanggal_absen')->nullable();
            $table->string('nik')->nullable();
            $table->string('nama')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->string('alamat')->nullable();
            $table->integer('bb')->nullable();
            $table->integer('tb')->nullable();
            $table->integer('lp')->nullable();
            $table->integer('lila')->nullable();
            $table->integer('sistole')->nullable();
            $table->integer('diastole')->nullable();
            $table->integer('au')->nullable();
            $table->integer('gda')->nullable();
            $table->integer('kol')->nullable();
            $table->string('ket')->nullable();
            $table->integer('bmi')->nullable();
            $table->string('hasil')->nullable();
            $table->string('status')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absen_dewasas');
    }
};
