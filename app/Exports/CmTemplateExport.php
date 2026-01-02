<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class CmTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
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
    }
}
