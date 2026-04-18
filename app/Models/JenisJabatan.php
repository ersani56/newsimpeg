<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisJabatan extends Model
{
    protected $table = 'jenis_jabatans';
    protected $primaryKey = 'jenis_jabatan_id';
    public $incrementing = false;
    protected $fillable = [
        'jenis_jabatan_id',
        'nama',
    ];
}
