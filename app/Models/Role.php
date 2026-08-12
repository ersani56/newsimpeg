<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_admin',
        'menu_permissions',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'menu_permissions' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
