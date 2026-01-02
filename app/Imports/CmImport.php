<?php

namespace App\Imports;

use App\Models\Cm;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CmImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Handle ATD date conversion
        $atd = null;
        if (isset($row['atd'])) {
            try {
                if (is_numeric($row['atd'])) {
                    $atd = Date::excelToDateTimeObject($row['atd']);
                } else {
                    $atd = Carbon::parse($row['atd']);
                }
            } catch (\Exception $e) {
                // Keep null if parse fails
            }
        }

        return Cm::updateOrCreate(
            [
                'container' => $row['container'] ?? '-',
                'cm'        => $row['cm'] ?? '-',
                'seal'      => $row['seal'] ?? null,
            ],
            [
                'ppcw'       => $row['ppcw'] ?? '-',
                'shipper'    => $row['shipper'] ?? '-',
                'consignee'  => $row['consignee'] ?? '-',
                'status'     => $row['status'] ?? '-',
                'commodity'  => $row['commodity'],
                'size'       => $row['size'] ?? '-',
                'berat'      => filter_var($row['berat'] ?? 0, FILTER_SANITIZE_NUMBER_INT),
                'keterangan' => $row['keterangan'],
                'atd'        => $atd,
            ]
        );
    }
}
