<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatJabatan extends Model
{
    protected $table = 'r_jabatans';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pns_id',
        'nip',
        'jenis_jabatan_id',
        'jabatan_id',
        'unor_id',
        'tmt_jabatan',
        'nomor_sk',
        'tanggal_sk',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tmt_jabatan' => 'date',
        'tanggal_sk' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi dengan model Pegawai (PNS)
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pns_id', 'pns_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

}
