<?php

namespace App\Exports;

use App\Models\Cm;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;

class CmExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        $query = Cm::latest();

        if ($this->search) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('ppcw', 'like', "%{$search}%")
                  ->orWhere('container', 'like', "%{$search}%")
                  ->orWhere('shipper', 'like', "%{$search}%")
                  ->orWhere('consignee', 'like', "%{$search}%")
                  ->orWhere('cm', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function map($cm): array
    {
        return [
            $cm->ppcw,
            $cm->container,
            $cm->seal,
            $cm->shipper,
            $cm->consignee,
            $cm->status,
            $cm->commodity,
            $cm->size,
            $cm->berat,
            $cm->cm,
            $cm->atd ? $cm->atd->format('d-m-Y') : null,
            $cm->keterangan,
        ];
    }

    public function headings(): array
    {
        return [
            'PPCW',
            'Container',
            'Seal',
            'Shipper',
            'Consignee',
            'Status',
            'Commodity',
            'Size',
            'Berat',
            'CM',
            'ATD',
            'Keterangan',
        ];
    }
}
