<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'atd' => 'date',
        'submit_so' => 'date',
    ];
}
