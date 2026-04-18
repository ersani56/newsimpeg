<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Golongan extends Model
{
    protected $table = 'golongans';
    protected $primaryKey = 'gol_id';
    public $incrementing = false;
    protected $fillable = [
        'golru',
        'pangkat',
    ];
}
