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
        return $this->belongsTo(Agama::class, 'agama_id');
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pend_id','pendidikan_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }
    public function riwayatPangkat()
    {
        return $this->belongsTo(RiwayatPangkat::class, 'r_pangkat_id');
    }

    // Relasi ke Riwayat Jabatan Terakhir
    public function riwayatJabatan()
    {
        return $this->belongsTo(RiwayatJabatan::class, 'r_jabatan_id');
    }

    // Relasi ke Unit Organisasi (Snapshot)
    public function unor()
    {
        return $this->belongsTo(Unor::class, 'unit_organisasi_id');
    }

    public function unitKerja() {
        return $this->belongsTo(Unor::class, 'unit_kerja_id');
    }

    public function golongan() {
        return $this->belongsTo(Golongan::class, 'golongan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisPegawai()
    {
        return $this->belongsTo(JenisPegawai::class);
    }

    public function kedudukanHukum() {
        return $this->belongsTo(KedudukanHukum::class, 'kedudukan_hukum_id');
    }

}


