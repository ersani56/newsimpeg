<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{

    protected $fillable = [
        'nip',
        'nama',
        'agama_id',
        'pendidikan_id',
        'jabatan_id',
        'unit_kerja_id',
    ];

    public function agama()
    {
        return $this->belongsTo(Agama::class);
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'pendidikan_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(Unor::class, 'unor_id', 'unor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class);
    }

    public function jenisPegawai()
    {
        return $this->belongsTo(JenisPegawai::class);
    }

    public function kedudukanHukum()
    {
        return $this->belongsTo(KedudukanHukum ::class,'kedudukan_hukum_id','kedudukan_hukum_id');
    }

    public function riwayatPangkat()
    {
        return $this->hasMany(RiwayatPangkat::class, 'pns_id','pns_id');
    }

    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatan::class, 'pns_id', 'pns_id')
            ->orderBy('tmt_jabatan', 'desc');
    }

}


