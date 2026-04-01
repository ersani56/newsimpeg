<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $fillable = [
        'nama',
        'eselon',
        'jenis_jabatan_id',
    ];

    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatan::class, 'jabatan_id', 'jabatan_id');
    }
}
