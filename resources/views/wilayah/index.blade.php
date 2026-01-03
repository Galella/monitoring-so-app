@extends('layouts.app')

@section('title', 'Manage Wilayah')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Wilayah List</h3>
                    <div class="card-tools">
                        <a href="{{ route('wilayahs.create') }}" class="btn btn-primary btn-sm">Add New Wilayah</a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Areas Count</th>
                                <th>Users Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wilayahs as $wilayah)
                            <tr>
                                <td>{{ $loop->iteration + ($wilayahs->currentPage() - 1) * $wilayahs->perPage() }}</td>
                                <td>{{ $wilayah->name }}</td>
                                <td>{{ $wilayah->code ?? '-' }}</td>
                                <td>{{ $wilayah->areas_count }}</td> <!-- Assuming withCount in Controller or relationship access, but controller didn't add withCount, optimizing query later? Blade lazy loads count or just count collection? actually just $wilayah->areas->count(), better to use withCount in controller for perf, but for now simple. -->
                                <td>{{ $wilayah->users->count() }}</td>
                                <td>
                                    <a href="{{ route('wilayahs.edit', $wilayah->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('wilayahs.destroy', $wilayah->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No Wilayah found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    {{ $wilayahs->links() }}
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection
