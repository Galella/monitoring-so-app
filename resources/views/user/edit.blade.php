@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit User</h3>
                </div>
                <!-- /.card-header -->
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password">New Password (Leave blank to keep current password)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
                        </div>
                        
                        <div class="form-group">
                            <label for="role_id">Role</label>
                            <select name="role_id" class="form-control @error('role_id') is-invalid @enderror" id="role_id" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="wilayah_id">Wilayah</label>
                            <select name="wilayah_id" class="form-control @error('wilayah_id') is-invalid @enderror" id="wilayah_id">
                                <option value="">Select Wilayah</option>
                                @foreach($wilayahs as $wilayah)
                                    <option value="{{ $wilayah->id }}" {{ old('wilayah_id', $user->wilayah_id) == $wilayah->id ? 'selected' : '' }}>{{ $wilayah->name }}</option>
                                @endforeach
                            </select>
                            @error('wilayah_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="area_id">Area</label>
                            <select name="area_id" class="form-control @error('area_id') is-invalid @enderror" id="area_id">
                                <option value="">Select Area</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_id', $user->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }} ({{ $area->wilayah->name ?? '-' }})</option>
                                @endforeach
                            </select>
                            @error('area_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- /.card-body -->
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection