<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $fillable = ['name', 'code'];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
