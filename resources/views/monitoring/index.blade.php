@extends('layouts.app')

@section('title', 'Monitoring & Reconciliation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <div class="card card-purple card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="monitoring-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $currentStatus === 'all' ? 'active' : '' }}" href="{{ route('monitoring.index', ['status' => 'all', 'search' => request('search')]) }}">
                                All Data <span class="badge badge-secondary right ml-2">{{ $counts['all'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $currentStatus === 'matched' ? 'active' : '' }}" href="{{ route('monitoring.index', ['status' => 'matched', 'search' => request('search')]) }}">
                                Matched <span class="badge badge-success right ml-2">{{ $counts['matched'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $currentStatus === 'unmatched_cm' ? 'active' : '' }}" href="{{ route('monitoring.index', ['status' => 'unmatched_cm', 'search' => request('search')]) }}">
                                Missing Coin <span class="badge badge-warning right ml-2">{{ $counts['unmatched_cm'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $currentStatus === 'unmatched_coin' ? 'active' : '' }}" href="{{ route('monitoring.index', ['status' => 'unmatched_coin', 'search' => request('search')]) }}">
                                Missing CM <span class="badge badge-danger right ml-2">{{ $counts['unmatched_coin'] }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <!-- Search Form inside Card Body to align with content -->
                    <!-- Search and Export -->
                    <div class="d-flex justify-content-end mb-3">
                        <form action="{{ route('monitoring.index') }}" method="GET" class="form-inline mr-2">
                            <input type="hidden" name="status" value="{{ $currentStatus }}">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control" placeholder="Search Container / CM" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <a href="{{ route('monitoring.export', ['status' => $currentStatus, 'search' => request('search')]) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-nowrap">
                            <thead>
                                <tr class="text-center bg-light">
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 20%" class="border-right border-dark">Key Identifier</th>
                                    <th colspan="3" class="bg-white border-right">CM Data (Operasional)</th>
                                    <th colspan="3" class="bg-white">Coin Data (Order/Keuangan)</th>
                                </tr>
                                <tr class="text-center text-sm text-muted">
                                    <th></th>
                                    <th class="border-right border-dark">CM | Container</th>
                                    
                                    <!-- CM Cols -->
                                    <th>ATD</th>
                                    <th>PPCW</th>
                                    <th class="border-right">Status</th>
                                    
                                    <!-- Coin Cols -->
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>SO #</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr>
                                    <td class="align-middle text-center">
                                        @if($item->status === 'MATCHED')
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> MATCHED</span>
                                        @elseif($item->status === 'UNMATCHED_CM')
                                            <span class="badge badge-warning text-white"><i class="fas fa-exclamation-triangle"></i> MISSING COIN</span>
                                            <!-- <br><small class="text-muted">No Order Data</small> -->
                                        @elseif($item->status === 'UNMATCHED_COIN')
                                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> MISSING CM</span>
                                            <!-- <br><small class="text-muted">No Ops Data</small> -->
                                        @endif
                                    </td>
                                    
                                    <td class="align-middle border-right border-dark font-weight-bold">
                                        <div class="d-flex flex-column">
                                            <span class="text-primary">{{ $item->key_cm }}</span>
                                            <span class="text-dark">{{ $item->key_container }}</span>
                                        </div>
                                    </td>

                                    <!-- CM DATA -->
                                    @if($item->cm_data)
                                        <td class="align-middle">{{ $item->cm_data->atd ? $item->cm_data->atd->format('d-m-Y') : '-' }}</td>
                                        <td class="align-middle">{{ Str::limit($item->cm_data->ppcw, 15) }}</td>
                                        <td class="align-middle border-right">{{ $item->cm_data->status }}</td>
                                    @else
                                        <td colspan="3" class="align-middle bg-light border-right text-center text-muted font-italic">
                                            -
                                        </td>
                                    @endif

                                    <!-- COIN DATA -->
                                    @if($item->coin_data)
                                        <td class="align-middle">{{ $item->coin_data->order_number }}</td>
                                        <td class="align-middle">{{ Str::limit($item->coin_data->customer, 15) }}</td>
                                        <td class="align-middle">{{ $item->coin_data->so }}</td>
                                    @else
                                        <td colspan="3" class="align-middle bg-light text-center text-muted font-italic">
                                            -
                                        </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No Data Found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    {{ $paginator->withQueryString()->links() }}
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection
