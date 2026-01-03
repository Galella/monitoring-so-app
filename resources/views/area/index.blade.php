@extends('layouts.app')

@section('title', 'Manage Areas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Area List</h3>
                    <div class="card-tools">
                        <a href="{{ route('areas.create') }}" class="btn btn-primary btn-sm">Add New Area</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Wilayah</th>
                                <th>Area Name</th>
                                <th>Code</th>
                                <th>Users Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($areas as $area)
                            <tr>
                                <td>{{ $loop->iteration + ($areas->currentPage() - 1) * $areas->perPage() }}</td>
                                <td>{{ $area->wilayah->name ?? '-' }}</td>
                                <td>{{ $area->name }}</td>
                                <td>{{ $area->code ?? '-' }}</td>
                                <td>{{ $area->users->count() }}</td>
                                <td>
                                    <a href="{{ route('areas.edit', $area->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('areas.destroy', $area->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No Areas found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    {{ $areas->links() }}
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection
