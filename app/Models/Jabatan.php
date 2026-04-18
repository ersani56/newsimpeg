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
        'jenis_jabatan_id',
        'unor_id',
        'nama',
        'eselon',
    ];

    public function rJabatans()
    {
        return $this->hasMany(RiwayatJabatan::class, 'jabatan_id');
    }
    public function jenisJabatan()
    {
        return $this->belongsTo(JenisJabatan::class, 'jenis_jabatan_id', 'jenis_jabatan_id');
    }
    public function unor()
    {
        return $this->belongsTo(Unor::class, 'unor_id', 'unor_id');
    }
}
