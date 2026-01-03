<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Cm extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll()
        ->logOnlyDirty();
    }

    protected $fillable = [
        'ppcw',
        'container',
        'seal',
        'shipper',
        'consignee',
        'status',
        'commodity',
        'size',
        'berat',
        'keterangan',
        'cm',
        'atd',
        'wilayah_id',
        'area_id',
    ];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function scopeForUser($query, $user)
    {
        // Super Admin sees everything
        if ($user->role->name === 'Super Admin') {
            return $query;
        }

        // Any other user with location assignment is scoped
        if ($user->wilayah_id) {
            $query->where('cms.wilayah_id', $user->wilayah_id);
        }
        
        if ($user->area_id) {
            $query->where('cms.area_id', $user->area_id);
        }
        
        return $query;
    }

    protected $casts = [
        'atd' => 'date',
    ];
}
