@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Role</h3>
                </div>
                <!-- /.card-header -->
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Role Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description" rows="3">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Permissions</label>
                            
                            @php
                                $groupedPermissions = $permissions->groupBy(function($item) {
                                    if (str_contains($item->name, 'users')) return 'User Management';
                                    if (str_contains($item->name, 'roles')) return 'Role Management';
                                    if (str_contains($item->name, 'wilayahs')) return 'Wilayah Management';
                                    if (str_contains($item->name, 'areas')) return 'Area Management';
                                    if (str_contains($item->name, 'monitoring_so')) return 'Monitoring SO Module'; // Check specific first
                                    if (str_contains($item->name, 'monitoring')) return 'Monitoring Dashboard';
                                    if (str_contains($item->name, 'cms')) return 'CM Data Management';
                                    if (str_contains($item->name, 'coins')) return 'Coin Data Management';
                                    
                                    return 'Other Permissions';
                                });
                                $rolePermissions = $role->permissions->pluck('id')->toArray();
                            @endphp

                            <div class="row">
                                @foreach($groupedPermissions as $groupName => $perms)
                                <div class="col-md-12">
                                    <div class="card card-secondary card-outline collapsed-card">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <div class="custom-control custom-checkbox d-inline">
                                                    <input class="custom-control-input parent-checkbox" type="checkbox" id="select_all_{{ Str::slug($groupName) }}" data-group="{{ Str::slug($groupName) }}">
                                                    <label for="select_all_{{ Str::slug($groupName) }}" class="custom-control-label">{{ $groupName }}</label>
                                                </div>
                                            </h5>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="display: none;"> <!-- Start collapsed -->
                                            <div class="row">
                                                @foreach($perms as $permission)
                                                <div class="col-md-3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input child-checkbox group-{{ Str::slug($groupName) }}" type="checkbox" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                        <label for="perm_{{ $permission->id }}" class="custom-control-label">{{ $permission->display_name }}</label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Role</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle Select All click
        $('.parent-checkbox').change(function() {
            var group = $(this).data('group');
            var isChecked = $(this).is(':checked');
            $('.group-' + group).prop('checked', isChecked);
        });

        // Handle Child checkbox click to update Select All state
        $('.child-checkbox').change(function() {
            var groupClass = $(this).attr('class').match(/group-[\w-]+/)[0];
            var group = groupClass.replace('group-', '');
            var allChecked = $('.group-' + group).length === $('.group-' + group + ':checked').length;
            $('#select_all_' + group).prop('checked', allChecked);
        });

        // Initial check for Select All checkboxes on load
        $('.parent-checkbox').each(function() {
            var group = $(this).data('group');
            var allChecked = $('.group-' + group).length === $('.group-' + group + ':checked').length;
            if ($('.group-' + group).length > 0) {
                $(this).prop('checked', allChecked);
            }
        });
    });
</script>
@endpush
@endsection