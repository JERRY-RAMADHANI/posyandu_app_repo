<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataBalita extends Model
{
    protected $table = 'data_balitas';

    protected $fillable = [
        'no_reg',
        'nama',
        'nik',
        'tanggal_lahir',
        'usia',
        'jenis_kelamin',
        'nama_ortu',
        'alamat',
        'rt',
        'rw',
    ];
}
