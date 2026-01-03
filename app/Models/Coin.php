<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Coin extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll()
        ->logOnlyDirty();
    }

    protected $fillable = [
        'cm',
        'order_number',
        'container',
        'seal',
        'p20',
        'p40',
        'po',
        'kereta',
        'atd',
        'customer',
        'stasiun_asal',
        'stasiun_tujuan',
        'gudang_asal',
        'gudang_tujuan',
        'jenis',
        'service',
        'payment',
        'so',
        'submit_so',
        'nominal_ppn',
        'sa_ppn',
        'loading_ppn',
        'unloading_ppn',
        'trucking_orig_ppn',
        'trucking_dest_ppn',
        'sa',
        'loading',
        'unloading',
        'trucking_orig',
        'trucking_dest',
        'nominal',
        'klaim',
        'dokumen',
        'alur_dokumen',
        'berat',
        'isi_barang',
        'ppcw',
        'owner',
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
            $query->where('coins.wilayah_id', $user->wilayah_id);
        }
        
        if ($user->area_id) {
            $query->where('coins.area_id', $user->area_id);
        }
        
        return $query;
    }

    protected $casts = [
        'atd' => 'date',
        'submit_so' => 'date',
    ];
}
