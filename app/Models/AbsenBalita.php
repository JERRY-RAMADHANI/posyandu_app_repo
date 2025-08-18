<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenBalita extends Model
{
    protected $table = 'absen_balitas'; // pastikan sesuai nama tabel

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
        'lk',
        'll',
        'ket',
    ];
}
