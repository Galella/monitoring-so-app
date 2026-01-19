<?php

namespace App\Imports;

use App\Models\Cm;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Maatwebsite\Excel\Concerns\WithValidation;

class CmImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user = auth()->user();
        $areaId = $user->area_id ?? null;
        $wilayahId = $user->wilayah_id ?? null;

        // If Super Admin (No Area), try to detect Area from CM code
        if (!$areaId) {
            $cmCode = strtoupper($row['cm'] ?? '');
            if (str_contains($cmCode, 'KLI')) {
                $areaId = 2; // Klari
                $wilayahId = 1; 
            } elseif (str_contains($cmCode, 'SAO')) {
                $areaId = 1; // Sungai Lagoa
                $wilayahId = 1;
            } elseif (str_contains($cmCode, 'JICT')) {
                $areaId = 3; // JICT
                $wilayahId = 1;
            }
        }
        
        $areaCode = $areaId === 3 ? 'JICT' : ($user->area->code ?? null);

        // Handle ATD date conversion (Used for both JICT and Standard if available)
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

        // --- Logic Khusus Area JICT ---
        if ($areaCode === 'JICT') {
            // Gabungkan info tambahan ke 'keterangan'
            $keteranganParts = array_filter([
                $row['kapal'] ? 'Kapal: ' . $row['kapal'] : null,
                $row['voyage'] ? 'Voy: ' . $row['voyage'] : null,
                $row['agen'] ? 'Agen: ' . $row['agen'] : null,
                $row['forwarder'] ? 'Fwd: ' . $row['forwarder'] : null,
                $row['bl'] ?? $row['b_l'] ? 'B/L: ' . ($row['bl'] ?? $row['b_l']) : null,
                $row['no_dokumen'] ? 'No Dok: ' . $row['no_dokumen'] : null,
                $row['tanggal_dokumen'] ? 'Tgl Dok: ' . $row['tanggal_dokumen'] : null,
            ]);

            $keterangan = implode(', ', $keteranganParts);

            return Cm::updateOrCreate(
                [
                    'container' => $row['container'] ?? '-',
                    'cm'        => $row['cm'] ?? '-',
                    'seal'      => $row['seal'] ?? null,
                ],
                [
                    'ppcw'       => $row['ppcw'] ?? '-',
                    'shipper'    => $row['shipper'] ?? '-',
                    'consignee'  => '-', // Default
                    'status'     => $row['status'] ?? '-',
                    'commodity'  => $row['commodity'] ?? null,
                    'size'       => $row['size'] ?? '-',
                    'berat'      => filter_var($row['weight'] ?? 0, FILTER_SANITIZE_NUMBER_INT), // Map weight -> berat
                    'keterangan' => $keterangan,
                    'atd'        => $atd,
                    'atd'        => $atd,
                    'wilayah_id' => $wilayahId,
                    'area_id'    => $areaId,
                ]
            );
        }

        // --- Logic Standar (Non-JICT) ---

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
                'atd'        => $atd,
                'wilayah_id' => $wilayahId,
                'area_id'    => $areaId,
            ]
        );
    }

    public function rules(): array
    {
        $user = auth()->user();
        $areaCode = $user->area->code ?? null;

        if ($areaCode === 'JICT') {
            return [
                'container' => 'required',
                'cm'        => 'required',
                'ppcw'      => 'required',
                'size'      => 'required',
                // Shipper, consignee, status NOT required for JICT
            ];
        }

        return [
            'container' => 'required',
            'cm' => 'required',
            'ppcw' => 'required',
            'shipper' => 'required',
            'consignee' => 'required',
            'status' => 'required',
            'size' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'container.required' => 'Kolom Container harus diisi',
            'cm.required' => 'Kolom CM harus diisi',
            'ppcw.required' => 'Kolom PPCW harus diisi',
            'shipper.required' => 'Kolom Shipper harus diisi',
            'consignee.required' => 'Kolom Consignee harus diisi',
            'status.required' => 'Kolom Status harus diisi',
            'size.required' => 'Kolom Size harus diisi',
        ];
    }
}
