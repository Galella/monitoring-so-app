@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Admin Dashboard</h3>
                </div>
                <div class="card-body">
                    <p>Welcome to the admin dashboard, {{ auth()->user()->name }}!</p>
                    <p>You have administrator privileges.</p>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Volume per Train (Kereta) - Last 6 Months</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart">
                                        <canvas id="volumeChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Data Reconciliation Widgets -->
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $stats['matched'] }}</h3>
                                    <p>Matched Records</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-link"></i>
                                </div>
                                <a href="{{ route('monitoring.index', ['status' => 'matched']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $stats['unmatched_cm'] }}</h3>
                                    <p>Missing Coin (Unmatched CM)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <a href="{{ route('monitoring.index', ['status' => 'unmatched_cm']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $stats['unmatched_coin'] }}</h3>
                                    <p>Missing CM (Unmatched Coin)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <a href="{{ route('monitoring.index', ['status' => 'unmatched_coin']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>

                    @if(!auth()->user()->area_id)
                    <h5 class="mt-4 mb-2">SO Status (Matched Only)</h5>
                    <div class="row">
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['so_system'] }}</h3>
                                    <p>SO from System</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-server"></i>
                                </div>
                                <a href="{{ route('monitoring-so.index', ['status' => 'system']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $stats['so_manual'] }}</h3>
                                    <p>SO Manual</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-keyboard"></i>
                                </div>
                                <a href="{{ route('monitoring-so.index', ['status' => 'manual']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-secondary">
                                <div class="inner">
                                    <h3>{{ $stats['so_pending'] }}</h3>
                                    <p>SO Not Submitted</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <a href="{{ route('monitoring-so.index', ['status' => 'not_submitted']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    @endif
                
                    @if(!auth()->user()->wilayah_id)
                    <!-- Original Widgets (Moved down or kept as needed) -->
                    <h5 class="mt-4 mb-2">System Management</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="small-box bg-light">
                                <div class="inner">
                                    <h3>Users</h3>
                                    <p>Manage system users</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="small-box bg-light">
                                <div class="inner">
                                    <h3>Roles</h3>
                                    <p>Manage user roles</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <a href="{{ route('roles.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    $(function () {
        var chartData = {
            labels  : {!! json_encode($stats['volume_labels'] ?? []) !!},
            datasets: {!! json_encode($stats['volume_datasets'] ?? []) !!}
        }

        var barChartCanvas = $('#volumeChart').get(0).getContext('2d')
        var barChartData = $.extend(true, {}, chartData)

        var barChartOptions = {
            responsive              : true,
            maintainAspectRatio     : false,
            datasetFill             : false,
            scales: {
                xAxes: [{
                    // stacked: true, // Grouped by default
                    gridLines : {
                        display : false,
                    }
                }],
                yAxes: [{
                    // stacked: true, // Grouped by default
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    },
                    gridLines : {
                        display : true,
                    }
                }]
            }
        }

        new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        })
    })
</script>
@endpush
@endsection