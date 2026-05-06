<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    protected $table = 'pendidikans';

    protected $primaryKey = 'pendidikan_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'pendidikan_id',
        'tingkat_pendidikan_id',
        'nama',
    ];

    public function tingkatPendidikan()
    {
        return $this->belongsTo(
            TingkatPendidikan::class,
            'tingkat_pendidikan_id',
            'tingkat_pendidikan_id'
        );
    }
}
