<!DOCTYPE html>
<html>
<head>
    <title>Laporan Monitoring SO</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #444; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-customer { background-color: #e2e3e5; font-weight: bold; }
        .bg-po { background-color: #f8f9fa; font-weight: bold; color: #004085; }
        .badge { padding: 2px 4px; border-radius: 3px; color: white; font-size: 9px; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .stats { margin-left: 10px; font-weight: normal; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Monitoring SO</h2>
        <p>Dicetak pada: {{ now()->format('d-M-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20px">#</th>
                <th>Container</th>
                <th>Seal</th>
                <th>CM</th>
                <th>ATD</th>
                <th>Asal</th>
                <th>Tujuan</th>
                <th>Nominal PPN</th>
                <th>SO Number</th>
                <th>Submit Date</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $currentCustomer = null; 
                $currentPO = null;
                $no = 1;
            @endphp
            @foreach($items as $item)
                @if($currentCustomer !== $item->customer)
                    @php 
                        $currentCustomer = $item->customer; 
                        $custStats = $customerStats[$currentCustomer] ?? null;
                        $currentPO = null; 
                    @endphp
                    <tr class="bg-customer">
                        <td colspan="10">
                            {{ $currentCustomer }}
                            @if($custStats)
                                <span class="stats">
                                    (Total Cont: {{ $custStats->count_container }} | Total PPN: Rp {{ number_format($custStats->sum_ppn, 0, ',', '.') }})
                                </span>
                            @endif
                        </td>
                    </tr>
                @endif

                @if($currentPO !== $item->po)
                    @php 
                        $currentPO = $item->po; 
                        $stats = $poStats[$currentPO] ?? null;
                    @endphp
                    <tr class="bg-po">
                        <td colspan="10" style="padding-left: 20px;">
                            PO: {{ $currentPO }}
                            @if($stats)
                                <span class="stats" style="color: #333;">
                                    (Total Cont: {{ $stats->count_container }} | Total PPN: Rp {{ number_format($stats->sum_ppn, 0, ',', '.') }})
                                </span>
                            @endif
                        </td>
                    </tr>
                @endif
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $item->container }}</td>
                    <td>{{ $item->seal }}</td>
                    <td>{{ $item->cm }}</td>
                    <td>{{ $item->atd ? $item->atd->format('d-M-Y') : '-' }}</td>
                    <td>{{ $item->stasiun_asal }}</td>
                    <td>{{ $item->stasiun_tujuan }}</td>
                    <td class="text-right">Rp {{ number_format($item->nominal_ppn, 0, ',', '.') }}</td>
                    <td>
                        @if (!$item->so || $item->so === 'Not Submitted')
                            <span class="badge badge-danger">Not Submitted</span>
                        @elseif (str_contains($item->so, 'Manual'))
                            <span class="badge badge-warning">{{ $item->so }}</span>
                        @else
                            <span class="badge badge-success">{{ $item->so }}</span>
                        @endif
                    </td>
                    <td>{{ $item->submit_so ? $item->submit_so->format('d-M-Y') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
