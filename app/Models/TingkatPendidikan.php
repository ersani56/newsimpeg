<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TingkatPendidikan extends Model
{
    protected $table = 'tingkat_pendidikans';

    protected $fillable = ['tingkat_pendidikan_id', 'nama'];

    public function riwayat()
    {
        return $this->hasMany(RiwayatPendidikan::class, 'tingkat_pendidikan_id', 'tingkat_pendidikan_id');
    }
}
