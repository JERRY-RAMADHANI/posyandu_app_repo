<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataBalita extends Model
{
    protected $table = 'data_balitas';

     protected $fillable = [
        'no_reg',
        'nik',
        'nama',
        'tanggal_lahir',
        'usia',
        'jenis_kelamin',
        'alamat',
        'rt',
        'rw',
        'nama_ortu',
        'panjang_lahir',
        'bb_lahir',
        'anak_ke',
        'buku_kia',
        'posyandu',
        'prov',
        'kab',
        'kec',
        'nik_2_dig',
        'tgl',
        'bln',
        'thn',
    ];
}
