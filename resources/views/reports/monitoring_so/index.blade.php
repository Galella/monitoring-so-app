@extends('layouts.app')

@section('title', 'Laporan Monitoring SO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Filter Card -->
            <div class="card card-primary card-outline collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Laporan</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <form action="{{ route('reports.monitoring-so.index') }}" method="GET">
                        <div class="row">
                            <!-- Date Filters -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jenis Tanggal</label>
                                    <select name="date_type" class="form-control form-control-sm">
                                        <option value="atd" {{ $dateType == 'atd' ? 'selected' : '' }}>Tanggal ATD</option>
                                        <option value="submit_so" {{ $dateType == 'submit_so' ? 'selected' : '' }}>Tanggal Submit SO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Dari Tanggal</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Sampai Tanggal</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Customer Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select name="customer" class="form-control form-control-sm select2">
                                        <option value="">-- Semua Customer --</option>
                                        @foreach($customers as $cust)
                                            <option value="{{ $cust }}" {{ $customer == $cust ? 'selected' : '' }}>{{ $cust }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Location Filters -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Stasiun Asal</label>
                                    <select name="stasiun_asal" class="form-control form-control-sm select2">
                                        <option value="">-- Semua Asal --</option>
                                        @foreach($stasiunAsals as $asal)
                                            <option value="{{ $asal }}" {{ $stasiunAsal == $asal ? 'selected' : '' }}>{{ $asal }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Stasiun Tujuan</label>
                                    <select name="stasiun_tujuan" class="form-control form-control-sm select2">
                                        <option value="">-- Semua Tujuan --</option>
                                        @foreach($stasiunTujuans as $tujuan)
                                            <option value="{{ $tujuan }}" {{ $stasiunTujuan == $tujuan ? 'selected' : '' }}>{{ $tujuan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status SO</label>
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="">-- Semua Status --</option>
                                        <option value="system" {{ $status == 'system' ? 'selected' : '' }}>System (Angka)</option>
                                        <option value="manual" {{ $status == 'manual' ? 'selected' : '' }}>Manual</option>
                                        <option value="not_submitted" {{ $status == 'not_submitted' ? 'selected' : '' }}>Not Submitted</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-right">
                                <a href="{{ route('reports.monitoring-so.index') }}" class="btn btn-default btn-sm mr-1">Reset</a>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search mr-1"></i> Tampilkan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Card -->
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Hasil Laporan</h3>
                        <div>
                            <a href="{{ route('reports.monitoring-so.export-pdf', request()->all()) }}" class="btn btn-danger btn-sm mr-1" target="_blank">
                                <i class="fas fa-file-pdf mr-1"></i> Export PDF
                            </a>
                            <a href="{{ route('reports.monitoring-so.export', request()->all()) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel mr-1"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover text-nowrap table-sm table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Container</th>
                                <th>Seal</th>
                                <th>CM</th>
                                <th>Customer</th>
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
                            @endphp
                            @forelse($items as $item)
                                @if($currentCustomer !== $item->customer)
                                    @php 
                                        $currentCustomer = $item->customer; 
                                        $custStats = $customerStats[$currentCustomer] ?? null;
                                        $currentPO = null; // Reset PO when customer changes
                                    @endphp
                                    <tr class="bg-secondary">
                                        <td colspan="11" class="font-weight-bold">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-building mr-2"></i> {{ $currentCustomer }}</span>
                                                @if($custStats)
                                                <span>
                                                    <span class="mr-3"><i class="fas fa-box mr-1"></i> Total Cont: {{ $custStats->count_container }}</span>
                                                    <span><i class="fas fa-money-bill-wave mr-1"></i> Total PPN: Rp {{ number_format($custStats->sum_ppn, 0, ',', '.') }}</span>
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                @if($currentPO !== $item->po)
                                    @php 
                                        $currentPO = $item->po; 
                                        $stats = $poStats[$currentPO] ?? null;
                                    @endphp
                                    <tr class="bg-light">
                                        <td colspan="11" class="font-weight-bold pl-4 text-primary">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-file-invoice mr-2"></i> PO: {{ $currentPO }}</span>
                                                @if($stats)
                                                <span>
                                                    <span class="mr-3 text-dark"><i class="fas fa-box mr-1"></i> Total Cont: {{ $stats->count_container }}</span>
                                                    <span class="text-dark"><i class="fas fa-money-bill-wave mr-1"></i> Total PPN: Rp {{ number_format($stats->sum_ppn, 0, ',', '.') }}</span>
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            <tr>
                                <td>{{ $items->firstItem() + $loop->index }}</td>
                                <td>{{ $item->container }}</td>
                                <td>{{ $item->seal }}</td>
                                <td>{{ $item->cm }}</td>
                                <td>{{ $item->customer }}</td>
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
                            @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">Data tidak ditemukan dengan filter yang dipilih.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                     {{ $items->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Select2 if available
    $(function () {
        // Only init if Select2 is loaded
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap4',
                allowClear: true,
                placeholder: 'Pilih opsi'
            });
        }
    });
</script>
@endpush
