<?php

namespace App\Exports;

use App\Models\Coin;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class MonitoringSoExport implements FromQuery, WithHeadings, WithMapping
{
    protected $status;
    protected $search;
    protected $user;

    public function __construct($status, $search, $user)
    {
        $this->status = $status;
        $this->search = $search;
        $this->user = $user;
    }

    public function query()
    {
        $query = Coin::query()
            ->join('cms', function($join) {
                $join->on('coins.container', '=', 'cms.container')
                     ->on('coins.cm', '=', 'cms.cm');
            })
            ->select('coins.*', 'cms.seal as cm_seal', 'cms.shipper as cm_shipper', 'cms.consignee as cm_consignee');

        // Apply Data Scoping
        $query->forUser($this->user);

        // Regex setup
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $numericPattern = $isSqlite ? '[0-9]*' : '^[0-9]+$';
        $numericOperator = $isSqlite ? 'GLOB' : 'REGEXP';

        // Apply Status Filter
        if ($this->status === 'system') {
            $query->where('coins.so', $numericOperator, $numericPattern);
        } elseif ($this->status === 'manual') {
            $query->where('coins.so', 'LIKE', '%Manual%');
        } elseif ($this->status === 'not_submitted') {
            $query->where(function($q) {
                $q->whereNull('coins.so')
                  ->orWhere('coins.so', '')
                  ->orWhere('coins.so', 'Not Submitted');
            });
        }
        // 'all' or default falls through here (no specific filter)

        // Apply Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('coins.container', 'like', "%{$this->search}%")
                  ->orWhere('coins.cm', 'like', "%{$this->search}%")
                  ->orWhere('coins.so', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('coins.po', 'asc');
    }

    public function headings(): array
    {
        return [
            'CM',
            'Order Number',
            'Container',
            'Seal',
            'Seal (CM)', // Extra info from CM
            'P20',
            'P40',
            'PO Number',
            'Kereta',
            'ATD',
            'Customer',
            'Stasiun Asal',
            'Stasiun Tujuan',
            'Gudang Asal',
            'Gudang Tujuan',
            'Jenis',
            'Service',
            'Payment',
            'SO Number',
            'Submit Date',
            'Nominal PPN',
            'SA PPN',
            'Loading PPN',
            'Unloading PPN',
            'Trucking Orig PPN',
            'Trucking Dest PPN',
            'SA',
            'Loading',
            'Unloading',
            'Trucking Orig',
            'Trucking Dest',
            'Nominal',
            'Klaim',
            'Dokumen',
            'Alur Dokumen',
            'Berat',
            'Isi Barang',
            'PPCW',
            'Owner',
            'Status' // Calculated
        ];
    }

    public function map($row): array
    {
        // Determine readable status
        $status = 'Not Submitted';
        if (preg_match('/^[0-9]+$/', $row->so)) {
            $status = 'System';
        } elseif (stripos($row->so, 'Manual') !== false) {
            $status = 'Manual';
        } elseif (!empty($row->so)) {
            $status = $row->so;
        }

        return [
            $row->cm,
            $row->order_number,
            $row->container,
            $row->seal,
            $row->cm_seal, // From Join
            $row->p20,
            $row->p40,
            $row->po,
            $row->kereta,
            $row->atd ? \Carbon\Carbon::parse($row->atd)->format('Y-m-d') : '',
            $row->customer,
            $row->stasiun_asal,
            $row->stasiun_tujuan,
            $row->gudang_asal,
            $row->gudang_tujuan,
            $row->jenis,
            $row->service,
            $row->payment,
            $row->so,
            $row->submit_so ? $row->submit_so->format('Y-m-d') : '-',
            $row->nominal_ppn,
            $row->sa_ppn,
            $row->loading_ppn,
            $row->unloading_ppn,
            $row->trucking_orig_ppn,
            $row->trucking_dest_ppn,
            $row->sa,
            $row->loading,
            $row->unloading,
            $row->trucking_orig,
            $row->trucking_dest,
            $row->nominal,
            $row->klaim,
            $row->dokumen,
            $row->alur_dokumen,
            $row->berat,
            $row->isi_barang,
            $row->ppcw,
            $row->owner,
            $status
        ];
    }
}
