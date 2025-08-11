<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenDewasa extends Model
{
    protected $table = 'absen_dewasas';

    protected $fillable = [
        'no_reg',
        'tanggal_absen',
        'nik',
        'nama',
        'tanggal_lahir',
        'usia',
        'alamat',
        'bb',
        'tb',
        'lp',
        'lila',
        'sistole',
        'diastole',
        'au',
        'gda',
        'kol',
        'ket',
        'bmi',
        'hasil',
        'status',
        'note',
    ];
}
