<?php

namespace App\Exports;

use App\Models\Coin;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;

class CoinExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        $query = Coin::latest();

        if ($this->search) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('container', 'like', "%{$search}%")
                  ->orWhere('customer', 'like', "%{$search}%")
                  ->orWhere('so', 'like', "%{$search}%")
                  ->orWhere('po', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function map($coin): array
    {
        return [
            $coin->cm,
            $coin->order_number,
            $coin->container,
            $coin->seal,
            $coin->p20,
            $coin->p40,
            $coin->po,
            $coin->kereta,
            $coin->atd ? $coin->atd->format('Y-m-d') : null,
            $coin->customer,
            $coin->stasiun_asal,
            $coin->stasiun_tujuan,
            $coin->gudang_asal,
            $coin->gudang_tujuan,
            $coin->jenis,
            $coin->service,
            $coin->payment,
            $coin->so,
            $coin->submit_so ? $coin->submit_so->format('Y-m-d') : null,
            $coin->nominal_ppn,
            $coin->sa_ppn,
            $coin->loading_ppn,
            $coin->unloading_ppn,
            $coin->trucking_orig_ppn,
            $coin->trucking_dest_ppn,
            $coin->sa,
            $coin->loading,
            $coin->unloading,
            $coin->trucking_orig,
            $coin->trucking_dest,
            $coin->nominal,
            $coin->klaim,
            $coin->dokumen,
            $coin->alur_dokumen,
            $coin->berat,
            $coin->isi_barang,
            $coin->ppcw,
            $coin->owner,
        ];
    }

    public function headings(): array
    {
        return [
            'CM', 'Order', 'Container', 'Seal', 'P20', 'P40', 'PO', 'Kereta', 'ATD', 'Customer', 'Stasiun Asal', 'Stasiun Tujuan', 'Gudang Asal', 'Gudang Tujuan', 'Jenis', 'Service', 'Payment', 'SO', 'Submit SO', 'Nominal PPN', 'SA PPN', 'Loading PPN', 'Unloading PPN', 'Trucking Orig PPN', 'Trucking Dest PPN', 'SA', 'Loading', 'Unloading', 'Trucking Orig', 'Trucking Dest', 'Nominal', 'Klaim', 'Dokumen', 'Alur Dokumen', 'Berat', 'Isi Barang', 'PPCW', 'Owner'
        ];
    }
}
