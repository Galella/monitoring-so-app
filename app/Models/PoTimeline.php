<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoTimeline extends Model
{
    protected $fillable = ['po_number', 'description', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
