<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class CoinTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        return new Collection([
            [
                'CM123', '1001', 'CNTR001', 'SEAL001', '1', '0', 'PO001', 'KRT01', '2023-01-01', 'Cust A', 'Stasiun A', 'Stasiun B', 'Gudang A', 'Gudang B', 'Jenis A', 'Service A', 'Payment A', 'SO001', '2023-01-02', '1000', '1000', '1000', '1000', '1000', '1000', '1000', '1000', '1000', '1000', '1000', '10000', 'Klaim A', 'Dok A', 'Alur A', '20000', 'Isi A', 'PPCW A', 'Owner A'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'CM', 'Order', 'Container', 'Seal', 'P20', 'P40', 'PO', 'Kereta', 'ATD', 'Customer', 'Stasiun Asal', 'Stasiun Tujuan', 'Gudang Asal', 'Gudang Tujuan', 'Jenis', 'Service', 'Payment', 'SO', 'Submit SO', 'Nominal PPN', 'SA PPN', 'Loading PPN', 'Unloading PPN', 'Trucking Orig PPN', 'Trucking Dest PPN', 'SA', 'Loading', 'Unloading', 'Trucking Orig', 'Trucking Dest', 'Nominal', 'Klaim', 'Dokumen', 'Alur Dokumen', 'Berat', 'Isi Barang', 'PPCW', 'Owner'
        ];
    }
}
