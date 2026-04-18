<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPegawai extends Model
{
    protected $table = 'jenis_pegawais';
    protected $primaryKey = 'jenis_pegawai_id';
    public $incrementing = false;
    protected $fillable = [
        'nama',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pns_id', 'pns_id');
    }
}
