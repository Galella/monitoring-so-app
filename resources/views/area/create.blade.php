@extends('layouts.app')

@section('title', 'Create Area')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Area</h3>
                </div>
                <form action="{{ route('areas.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="wilayah_id">Wilayah</label>
                            <select name="wilayah_id" class="form-control @error('wilayah_id') is-invalid @enderror" id="wilayah_id" required>
                                <option value="">Select Wilayah</option>
                                @foreach($wilayahs as $wilayah)
                                    <option value="{{ $wilayah->id }}" {{ old('wilayah_id') == $wilayah->id ? 'selected' : '' }}>{{ $wilayah->name }}</option>
                                @endforeach
                            </select>
                            @error('wilayah_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" id="code" value="{{ old('code') }}">
                            @error('code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('areas.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
