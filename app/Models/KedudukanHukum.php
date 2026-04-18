<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KedudukanHukum extends Model
{
    protected $table = 'kedudukan_hukums';
    protected $primaryKey = 'kedudukan_hukum_id';
    public $incrementing = false;

    protected $fillable = [
        'nama',
    ];
}
