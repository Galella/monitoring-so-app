@extends('layouts.app')

@section('title', 'Edit CM Data')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit CM Data</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('cms.update', $cm->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ppcw">PPCW <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ppcw') is-invalid @enderror" id="ppcw" name="ppcw" value="{{ old('ppcw', $cm->ppcw) }}" required>
                                    @error('ppcw')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="container">Container <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('container') is-invalid @enderror" id="container" name="container" value="{{ old('container', $cm->container) }}" required>
                                    @error('container')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="seal">Seal</label>
                                    <input type="text" class="form-control @error('seal') is-invalid @enderror" id="seal" name="seal" value="{{ old('seal', $cm->seal) }}">
                                    @error('seal')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="shipper">Shipper <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('shipper') is-invalid @enderror" id="shipper" name="shipper" value="{{ old('shipper', $cm->shipper) }}" required>
                                    @error('shipper')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="consignee">Consignee <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('consignee') is-invalid @enderror" id="consignee" name="consignee" value="{{ old('consignee', $cm->consignee) }}" required>
                                    @error('consignee')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="commodity">Commodity</label>
                                    <input type="text" class="form-control @error('commodity') is-invalid @enderror" id="commodity" name="commodity" value="{{ old('commodity', $cm->commodity) }}">
                                    @error('commodity')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('status') is-invalid @enderror" id="status" name="status" value="{{ old('status', $cm->status) }}" required>
                                    @error('status')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="size">Size <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('size') is-invalid @enderror" id="size" name="size" value="{{ old('size', $cm->size) }}" required>
                                    @error('size')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="berat">Berat (kg)</label>
                                    <input type="number" class="form-control @error('berat') is-invalid @enderror" id="berat" name="berat" value="{{ old('berat', $cm->berat) }}">
                                    @error('berat')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="cm">CM <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('cm') is-invalid @enderror" id="cm" name="cm" value="{{ old('cm', $cm->cm) }}" required>
                                    @error('cm')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="atd">ATD</label>
                                    <input type="date" class="form-control @error('atd') is-invalid @enderror" id="atd" name="atd" value="{{ old('atd', $cm->atd ? $cm->atd->format('Y-m-d') : '') }}">
                                    @error('atd')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $cm->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('cms.index') }}" class="btn btn-default float-right">Cancel</a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection
