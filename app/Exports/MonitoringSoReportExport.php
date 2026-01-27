<?php

namespace App\Exports;

use App\Models\Coin;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonitoringSoReportExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;
    protected $user;

    public function __construct(Request $request)
    {
        $this->filters = [
            'date_type' => $request->get('date_type', 'atd'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'customer' => $request->get('customer'),
            'status' => $request->get('status'),
            'stasiun_asal' => $request->get('stasiun_asal'),
            'stasiun_tujuan' => $request->get('stasiun_tujuan'),
        ];
        $this->user = auth()->user();
    }

    public function query()
    {
        $query = Coin::query()->select('coins.*');
        $query->forUser($this->user);

        $this->applyFilters($query);

        return $query->orderBy('customer', 'asc')->orderBy('po', 'asc');
    }

    protected function applyFilters($query)
    {
        $dateType = $this->filters['date_type'];
        $startDate = $this->filters['start_date'];
        $endDate = $this->filters['end_date'];
        $customer = $this->filters['customer'];
        $status = $this->filters['status'];
        $stasiunAsal = $this->filters['stasiun_asal'];
        $stasiunTujuan = $this->filters['stasiun_tujuan'];

        if ($startDate && $endDate) {
            $column = $dateType === 'submit_so' ? 'coins.submit_so' : 'coins.atd';
            $query->whereBetween($column, [$startDate, $endDate]);
        }
        if ($customer) {
            $query->where('coins.customer', $customer);
        }
        if ($stasiunAsal) {
            $query->where('coins.stasiun_asal', $stasiunAsal);
        }
        if ($stasiunTujuan) {
             $query->where('coins.stasiun_tujuan', $stasiunTujuan);
        }

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $numericPattern = $isSqlite ? '[0-9]*' : '^[0-9]+$';
        $numericOperator = $isSqlite ? 'GLOB' : 'REGEXP';

         if ($status === 'system') {
            $query->where('coins.so', $numericOperator, $numericPattern);
        } elseif ($status === 'manual') {
             $query->where('coins.so', 'LIKE', '%Manual%');
        } elseif ($status === 'not_submitted') {
             $query->where(function($q) {
                $q->whereNull('coins.so')
                  ->orWhere('coins.so', '')
                  ->orWhere('coins.so', 'Not Submitted');
            });
        }
    }

    public function headings(): array
    {
        return [
            'Customer',
            'PO Number',
            'Container',
            'Seal',
            'CM',
            'ATD',
            'Stasiun Asal',
            'Stasiun Tujuan',
            'Nominal PPN',
            'SO Number',
            'Submit Date',
        ];
    }

    public function map($row): array
    {
        return [
            $row->customer,
            $row->po,
            $row->container,
            $row->seal,
            $row->cm,
            $row->atd ? Carbon::parse($row->atd)->format('d-M-Y') : '-',
            $row->stasiun_asal,
            $row->stasiun_tujuan,
            $row->nominal_ppn,
            $row->so,
            $row->submit_so ? Carbon::parse($row->submit_so)->format('d-M-Y') : '-',
        ];
    }
}
