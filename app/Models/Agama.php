<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    use HasFactory;
    protected $table = 'agamas';
    protected $primaryKey = 'agama_id';
    public $incrementing = false;

    protected $fillable = [
        'nama',
    ];
}
