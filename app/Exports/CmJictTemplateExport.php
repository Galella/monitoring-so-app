<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class CmJictTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'container',
            'size',
            'ppcw',
            'weight',
            'kapal',
            'voyage',
            'agen',
            'forwarder',
            'commodity',
            'dokumen pabean',
            'no dokumen',
            'tanggal dokumen',
            'b/l',
            'cm',
            'seal',
            'atd',
            'shipper',
            'status',
        ];
    }
}
