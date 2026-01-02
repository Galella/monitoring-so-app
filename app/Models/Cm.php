<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cm extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'atd' => 'date',
    ];
}
