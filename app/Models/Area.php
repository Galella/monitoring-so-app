<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['wilayah_id', 'name', 'code'];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
