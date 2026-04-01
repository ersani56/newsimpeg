<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    protected $fillable = [
        'nama',
    ];
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pns_id', 'pns_id');
    }
}
