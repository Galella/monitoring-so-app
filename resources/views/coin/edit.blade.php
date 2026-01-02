@extends('layouts.app')

@section('title', 'Edit Coin Data')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit Coin Data: {{ $coin->order_number }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('coins.update', $coin->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Column 1: Core Identifiers -->
                            <div class="col-md-4">
                                <h5 class="text-primary"><i class="fas fa-info-circle"></i> Basic Info</h5>
                                <hr>
                                <div class="form-group">
                                    <label>Order Number <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="order_number" value="{{ old('order_number', $coin->order_number) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Customer <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="customer" value="{{ old('customer', $coin->customer) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>SO Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="so" value="{{ old('so', $coin->so) }}" required>
                                </div>
                                 <div class="form-group">
                                    <label>Submit SO</label>
                                    <input type="date" class="form-control" name="submit_so" value="{{ old('submit_so', $coin->submit_so ? $coin->submit_so->format('Y-m-d') : '') }}">
                                </div>
                                <div class="form-group">
                                    <label>PO Number</label>
                                    <input type="text" class="form-control" name="po" value="{{ old('po', $coin->po) }}">
                                </div>
                                <div class="form-group">
                                    <label>CM</label>
                                    <input type="text" class="form-control" name="cm" value="{{ old('cm', $coin->cm) }}">
                                </div>
                            </div>

                            <!-- Column 2: Logistics -->
                            <div class="col-md-4">
                                <h5 class="text-primary"><i class="fas fa-truck"></i> Logistics</h5>
                                <hr>
                                <div class="form-group">
                                    <label>Container <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="container" value="{{ old('container', $coin->container) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Seal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="seal" value="{{ old('seal', $coin->seal) }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>P20 <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="p20" value="{{ old('p20', $coin->p20) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>P40 <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="p40" value="{{ old('p40', $coin->p40) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Kereta <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="kereta" value="{{ old('kereta', $coin->kereta) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>ATD <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="atd" value="{{ old('atd', $coin->atd ? $coin->atd->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>

                            <!-- Column 3: Routes & Service -->
                            <div class="col-md-4">
                                <h5 class="text-primary"><i class="fas fa-map-marker-alt"></i> Route & Service</h5>
                                <hr>
                                <div class="row">
                                     <div class="col-6">
                                        <div class="form-group">
                                            <label>Stasiun Asal <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="stasiun_asal" value="{{ old('stasiun_asal', $coin->stasiun_asal) }}" required>
                                        </div>
                                     </div>
                                     <div class="col-6">
                                         <div class="form-group">
                                            <label>Stasiun Tujuan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="stasiun_tujuan" value="{{ old('stasiun_tujuan', $coin->stasiun_tujuan) }}" required>
                                        </div>
                                     </div>
                                </div>
                                <div class="row">
                                     <div class="col-6">
                                        <div class="form-group">
                                            <label>Gudang Asal</label>
                                            <input type="text" class="form-control" name="gudang_asal" value="{{ old('gudang_asal', $coin->gudang_asal) }}">
                                        </div>
                                     </div>
                                     <div class="col-6">
                                         <div class="form-group">
                                            <label>Gudang Tujuan</label>
                                            <input type="text" class="form-control" name="gudang_tujuan" value="{{ old('gudang_tujuan', $coin->gudang_tujuan) }}">
                                        </div>
                                     </div>
                                </div>
                                <div class="form-group">
                                    <label>Jenis <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="jenis" value="{{ old('jenis', $coin->jenis) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Service <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="service" value="{{ old('service', $coin->service) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Payment <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="payment" value="{{ old('payment', $coin->payment) }}" required>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5 class="text-info"><i class="fas fa-coins"></i> Financials</h5>
                        <div class="row">
                            @foreach(['nominal', 'sa', 'loading', 'unloading', 'trucking_orig', 'trucking_dest'] as $key)
                            <div class="col-md-2 col-6">
                                <div class="form-group">
                                    <label>{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                    <input type="number" class="form-control form-control-sm" name="{{ $key }}" value="{{ old($key, $coin->$key) }}" placeholder="Amount">
                                    <input type="number" class="form-control form-control-sm mt-1" name="{{ $key }}_ppn" value="{{ old($key.'_ppn', $coin->{$key.'_ppn'}) }}" placeholder="PPN">
                                </div>
                            </div>
                            @endforeach
                        </div>

                         <hr>
                        <h5 class="text-secondary"><i class="fas fa-box"></i> Additional Info</h5>
                        <div class="row">
                            <div class="col-md-3">
                                 <div class="form-group">
                                    <label>Isi Barang</label>
                                    <input type="text" class="form-control" name="isi_barang" value="{{ old('isi_barang', $coin->isi_barang) }}">
                                </div>
                            </div>
                             <div class="col-md-3">
                                 <div class="form-group">
                                    <label>Berat (Kg)</label>
                                    <input type="number" class="form-control" name="berat" value="{{ old('berat', $coin->berat) }}">
                                </div>
                            </div>
                             <div class="col-md-3">
                                 <div class="form-group">
                                    <label>PPCW</label>
                                    <input type="text" class="form-control" name="ppcw" value="{{ old('ppcw', $coin->ppcw) }}">
                                </div>
                            </div>
                             <div class="col-md-3">
                                 <div class="form-group">
                                    <label>Owner</label>
                                    <input type="text" class="form-control" name="owner" value="{{ old('owner', $coin->owner) }}">
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-4">
                                 <div class="form-group">
                                    <label>Dokumen</label>
                                    <input type="text" class="form-control" name="dokumen" value="{{ old('dokumen', $coin->dokumen) }}">
                                </div>
                            </div>
                             <div class="col-md-4">
                                 <div class="form-group">
                                    <label>Alur Dokumen</label>
                                    <input type="text" class="form-control" name="alur_dokumen" value="{{ old('alur_dokumen', $coin->alur_dokumen) }}">
                                </div>
                            </div>
                             <div class="col-md-4">
                                 <div class="form-group">
                                    <label>Klaim</label>
                                    <input type="text" class="form-control" name="klaim" value="{{ old('klaim', $coin->klaim) }}">
                                </div>
                            </div>
                        </div>


                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Coin Data</button>
                        <a href="{{ route('coins.index') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection
