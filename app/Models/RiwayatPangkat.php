<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPangkat extends Model
{
    protected $table = 'r_pangkats';

    protected $fillable = [
        'pns_id',
        'golongan_id',
        'tmt_golongan',
        'mk_tahun',
        'mk_bulan',
        'nomor_sk',
        'tanggal_sk',
    ];
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pns_id', 'pns_id');
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class);
    }
}
