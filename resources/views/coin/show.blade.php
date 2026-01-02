@extends('layouts.app')

@section('title', 'View Coin Data')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Coin Data Details: <strong>{{ $coin->order_number }}</strong></h3>
                    <div class="card-tools">
                         <a href="{{ route('coins.edit', $coin->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('coins.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- General Information -->
                        <div class="col-12 col-md-4">
                            <div class="card card-light">
                                <div class="card-header">
                                    <h3 class="card-title">General Information</h3>
                                </div>
                                <div class="card-body">
                                    <dl>
                                        <dt>Order Number</dt>
                                        <dd>{{ $coin->order_number }}</dd>
                                        <dt>Customer</dt>
                                        <dd>{{ $coin->customer }}</dd>
                                        <dt>SO Number</dt>
                                        <dd>{{ $coin->so }}</dd>
                                        <dt>PO Number</dt>
                                        <dd>{{ $coin->po ?? '-' }}</dd>
                                        <dt>CM</dt>
                                        <dd>{{ $coin->cm ?? '-' }}</dd>
                                        <dt>Submit SO</dt>
                                        <dd>{{ $coin->submit_so ? $coin->submit_so->format('d M Y') : '-' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Details -->
                        <div class="col-12 col-md-4">
                            <div class="card card-light">
                                <div class="card-header">
                                    <h3 class="card-title">Shipping Details</h3>
                                </div>
                                <div class="card-body">
                                    <dl>
                                        <dt>Container</dt>
                                        <dd>{{ $coin->container }}</dd>
                                        <dt>Seal</dt>
                                        <dd>{{ $coin->seal }}</dd>
                                        <dt>P20 / P40</dt>
                                        <dd>{{ $coin->p20 }} / {{ $coin->p40 }}</dd>
                                        <dt>Kereta / ATD</dt>
                                        <dd>{{ $coin->kereta }} / {{ $coin->atd ? $coin->atd->format('d M Y') : '-' }}</dd>
                                        <dt>Route</dt>
                                        <dd>{{ $coin->stasiun_asal }} <i class="fas fa-arrow-right text-muted mx-1"></i> {{ $coin->stasiun_tujuan }}</dd>
                                        <dt>Warehouse</dt>
                                        <dd>{{ $coin->gudang_asal ?? '-' }} <i class="fas fa-arrow-right text-muted mx-1"></i> {{ $coin->gudang_tujuan ?? '-' }}</dd>
                                        <dt>Service / Payment</dt>
                                        <dd>{{ $coin->service }} / {{ $coin->payment }}</dd>
                                        <dt>Jenis</dt>
                                        <dd>{{ $coin->jenis }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Cargo & Extras -->
                        <div class="col-12 col-md-4">
                             <div class="card card-light">
                                <div class="card-header">
                                    <h3 class="card-title">Cargo & Additional Info</h3>
                                </div>
                                <div class="card-body">
                                    <dl>
                                        <dt>Isi Barang</dt>
                                        <dd>{{ $coin->isi_barang ?? '-' }}</dd>
                                        <dt>Berat</dt>
                                        <dd>{{ $coin->berat ? number_format($coin->berat) . ' kg' : '-' }}</dd>
                                        <dt>PPCW</dt>
                                        <dd>{{ $coin->ppcw ?? '-' }}</dd>
                                        <dt>Owner</dt>
                                        <dd>{{ $coin->owner ?? '-' }}</dd>
                                        <dt>Dokumen / Alur</dt>
                                        <dd>{{ $coin->dokumen ?? '-' }} / {{ $coin->alur_dokumen ?? '-' }}</dd>
                                        <dt>Klaim</dt>
                                        <dd>{{ $coin->klaim ?? '-' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Financials -->
                        <div class="col-12">
                             <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Financial Data</h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Amount</th>
                                                <th>VAT (PPN) Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Nominal</td>
                                                <td>{{ $coin->nominal ? 'Rp ' . number_format($coin->nominal) : '-' }}</td>
                                                <td>{{ $coin->nominal_ppn ? 'Rp ' . number_format($coin->nominal_ppn) : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td>SA</td>
                                                <td>{{ $coin->sa ? 'Rp ' . number_format($coin->sa) : '-' }}</td>
                                                <td>{{ $coin->sa_ppn ? 'Rp ' . number_format($coin->sa_ppn) : '-' }}</td>
                                            </tr>
                                             <tr>
                                                <td>Loading</td>
                                                <td>{{ $coin->loading ? 'Rp ' . number_format($coin->loading) : '-' }}</td>
                                                <td>{{ $coin->loading_ppn ? 'Rp ' . number_format($coin->loading_ppn) : '-' }}</td>
                                            </tr>
                                             <tr>
                                                <td>Unloading</td>
                                                <td>{{ $coin->unloading ? 'Rp ' . number_format($coin->unloading) : '-' }}</td>
                                                <td>{{ $coin->unloading_ppn ? 'Rp ' . number_format($coin->unloading_ppn) : '-' }}</td>
                                            </tr>
                                             <tr>
                                                <td>Trucking Orig</td>
                                                <td>{{ $coin->trucking_orig ? 'Rp ' . number_format($coin->trucking_orig) : '-' }}</td>
                                                <td>{{ $coin->trucking_orig_ppn ? 'Rp ' . number_format($coin->trucking_orig_ppn) : '-' }}</td>
                                            </tr>
                                             <tr>
                                                <td>Trucking Dest</td>
                                                <td>{{ $coin->trucking_dest ? 'Rp ' . number_format($coin->trucking_dest) : '-' }}</td>
                                                <td>{{ $coin->trucking_dest_ppn ? 'Rp ' . number_format($coin->trucking_dest_ppn) : '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection
