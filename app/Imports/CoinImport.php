<?php

namespace App\Imports;

use App\Models\Coin;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class CoinImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Must match headings in Template
        // Keys are slugified headings (lowercase, underscores)
        
        $atd = null;
        if (isset($row['atd'])) {
             try {
                $atd = is_numeric($row['atd']) ? Date::excelToDateTimeObject($row['atd']) : Carbon::parse($row['atd']);
            } catch (\Exception $e) { $atd = null; }
        }

        $submit_so = null;
        if (isset($row['submit_so'])) {
             try {
                $submit_so = is_numeric($row['submit_so']) ? Date::excelToDateTimeObject($row['submit_so']) : Carbon::parse($row['submit_so']);
            } catch (\Exception $e) { $submit_so = null; }
        }

        return Coin::updateOrCreate(
            [
                'order_number' => $row['order'], // Check unique key
            ],
            [
                'cm' => $row['cm'] ?? null,
                'container' => $row['container'],
                'seal' => $row['seal'],
                'p20' => $row['p20'] ?? 0,
                'p40' => $row['p40'] ?? 0,
                'po' => $row['po'] ?? null,
                'kereta' => $row['kereta'],
                'atd' => $atd,
                'customer' => $row['customer'],
                'stasiun_asal' => $row['stasiun_asal'],
                'stasiun_tujuan' => $row['stasiun_tujuan'],
                'gudang_asal' => $row['gudang_asal'] ?? null,
                'gudang_tujuan' => $row['gudang_tujuan'] ?? null,
                'jenis' => $row['jenis'],
                'service' => $row['service'],
                'payment' => $row['payment'],
                'so' => $row['so'],
                'submit_so' => $submit_so,
                'nominal_ppn' => $row['nominal_ppn'] ?? 0,
                'sa_ppn' => $row['sa_ppn'] ?? 0,
                'loading_ppn' => $row['loading_ppn'] ?? 0,
                'unloading_ppn' => $row['unloading_ppn'] ?? 0,
                'trucking_orig_ppn' => $row['trucking_orig_ppn'] ?? 0,
                'trucking_dest_ppn' => $row['trucking_dest_ppn'] ?? 0,
                'sa' => $row['sa'] ?? 0,
                'loading' => $row['loading'] ?? 0,
                'unloading' => $row['unloading'] ?? 0,
                'trucking_orig' => $row['trucking_orig'] ?? 0,
                'trucking_dest' => $row['trucking_dest'] ?? 0,
                'nominal' => $row['nominal'] ?? 0,
                'klaim' => $row['klaim'] ?? null,
                'dokumen' => $row['dokumen'] ?? null,
                'alur_dokumen' => $row['alur_dokumen'] ?? null,
                'berat' => $row['berat'] ?? 0,
                'isi_barang' => $row['isi_barang'] ?? null,
                'ppcw' => $row['ppcw'] ?? null,
                'owner' => $row['owner'] ?? null,
                'wilayah_id' => auth()->user()->wilayah_id ?? null,
                'area_id'    => auth()->user()->area_id ?? null,
            ]
        );
    }
}
