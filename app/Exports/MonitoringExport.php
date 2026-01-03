<?php

namespace App\Exports;

use App\Models\Cm;
use App\Models\Coin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;
use App\Services\DataScopeService;

class MonitoringExport implements FromCollection, WithHeadings, WithMapping
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

    public function collection()
    {
        // Helper closures removed, using DataScopeService instead

        // Build Query
        $query = null;

        if ($this->status === 'matched') {
            $query = DB::table('cms')
                ->select('cm', 'container', DB::raw("'MATCHED' as status"))
                ->whereExists(DataScopeService::matchCondition());
            DataScopeService::apply($query, 'cms', $this->user);
                
        } elseif ($this->status === 'unmatched_cm') {
            $query = DB::table('cms')
                ->select('cm', 'container', DB::raw("'UNMATCHED_CM' as status"))
                ->whereNotExists(DataScopeService::matchCondition());
            DataScopeService::apply($query, 'cms', $this->user);
                
        } elseif ($this->status === 'unmatched_coin') {
            $query = DB::table('coins')
                ->select('cm', 'container', DB::raw("'UNMATCHED_COIN' as status"))
                ->whereNotExists(DataScopeService::reverseMatchCondition());
            DataScopeService::apply($query, 'coins', $this->user);
                
        } else {
            // ALL -> Union strategy
            $cmKeys = DB::table('cms')->select('cm', 'container');
            DataScopeService::apply($cmKeys, 'cms', $this->user);
                
            $coinKeys = DB::table('coins')->select('cm', 'container');
            DataScopeService::apply($coinKeys, 'coins', $this->user);

            $query = $coinKeys->union($cmKeys);
        }

        // Apply Search
        $finalQuery = DB::table(DB::raw("({$query->toSql()}) as keys_table"))
            ->mergeBindings($query); 

        if ($this->search) {
            $finalQuery->where(function($q) {
                $q->where('container', 'like', "%{$this->search}%")
                  ->orWhere('cm', 'like', "%{$this->search}%");
            });
        }

        // Fetch Keys (No pagination for export)
        $keys = $finalQuery->get();

        // Hydrate Data
        return $keys->map(function($key) {
            $computedStatus = $key->status ?? null;
            
            $cmData = Cm::forUser($this->user)
                        ->where('container', $key->container)
                        ->where('cm', $key->cm)
                        ->first();
                        
            $coinData = Coin::forUser($this->user)
                            ->where('container', $key->container)
                            ->where('cm', $key->cm)
                            ->first();

            if (!$computedStatus) {
                if ($cmData && $coinData) $computedStatus = 'MATCHED';
                elseif ($cmData && !$coinData) $computedStatus = 'UNMATCHED_CM';
                elseif (!$cmData && $coinData) $computedStatus = 'UNMATCHED_COIN';
            }

            return (object) [
                'key_cm' => $key->cm,
                'key_container' => $key->container,
                'cm_data' => $cmData,
                'coin_data' => $coinData,
                'status' => $computedStatus
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Filter Status',
            'Final Status',
            'Container',
            'CM Number',
            // CM Data
            'CM Date', 'Shipper', 'Consignee', 'Pol', 'Pod', 'Vessel', 'Voyage', 'Feeder Vessel', 'Feeder Voyage', 'Closing Date', 'Arrival Date', 'Bill Of Lading', 'Party Ref', 'Booking Number',
            // Coin Data
            'Coin Order No', 'Seal', 'Kereta', 'Customer', 'SO Number', 'Submit Date'
        ];
    }

    public function map($row): array
    {
        $cm = $row->cm_data;
        $coin = $row->coin_data;

        return [
            $this->status, // Filter Context
            $row->status,
            $row->key_container,
            $row->key_cm,
            
            // CM Data
            $cm ? $cm->cm_date : '-',
            $cm ? $cm->shipper : '-',
            $cm ? $cm->consignee : '-',
            $cm ? $cm->pol : '-',
            $cm ? $cm->pod : '-',
            $cm ? $cm->vessel_name : '-',
            $cm ? $cm->voyage_number : '-',
            $cm ? $cm->feeder_vessel : '-',
            $cm ? $cm->feeder_voyage : '-',
            $cm ? $cm->closing_date : '-',
            $cm ? $cm->arrival_date : '-',
            $cm ? $cm->bill_of_lading : '-',
            $cm ? $cm->party_ref : '-',
            $cm ? $cm->booking_number : '-',

            // Coin Data
            $coin ? $coin->order_number : '-',
            $coin ? $coin->seal : '-',
            $coin ? $coin->kereta : '-',
            $coin ? $coin->customer : '-',
            $coin ? $coin->so : '-',
            $coin && $coin->submit_so ? $coin->submit_so->format('Y-m-d') : '-',
        ];
    }
}
