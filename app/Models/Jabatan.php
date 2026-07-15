<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatans';
    protected $primaryKey = 'jabatan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'jabatan_id',
        'kel_jab',
        'unor_nama',
        'jabatan_nama',
        'eselon',
        'bup',
        'jenjang',
    ];

    public function rJabatans()
    {
        return $this->hasMany(RiwayatJabatan::class, 'jabatan_id');
    }
}
