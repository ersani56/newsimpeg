<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikan extends Model
{
    protected $table = 'r_pends';

    protected $fillable = [
        'pegawai_id',
        'pendidikan_id',
        'tingkat_pendidikan_id',
        'nama_sekolah',
        'tahun_lulus',
    ];

    // 🔗 Relasi ke Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    // 🔗 Relasi ke Pendidikan (UUID BKN)
    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'pendidikan_id');
    }

    // 🔗 Relasi ke Tingkat Pendidikan
    public function tingkat()
    {
        return $this->belongsTo(TingkatPendidikan::class, 'tingkat_pendidikan_id', 'tingkat_pendidikan_id');
    }
}
