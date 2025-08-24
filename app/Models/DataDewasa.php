<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataDewasa extends Model
{
    protected $table = 'data_dewasa';

    protected $fillable = [
        'nik',
        'no_reg',
        'nama',
        'tanggal_lahir',
        'umur',
        'jenis_kelamin',
        'alamat',
        'rt',
        'rw',
        'status',
        'bmi',
        'keterangan',
        'note'
    ];

    // public static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         // Ambil jumlah data yang ada
    //         $count = static::count();

    //         // No_reg = jumlah data + 1
    //         $model->no_reg = $count + 1;
    //     });
    // }

    public $timestamps = false;
}
