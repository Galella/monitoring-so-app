@extends('layouts.app')

@section('title', 'Coin Data Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Coin Data List</h3>
                    <div class="card-tools d-flex">
                        <form action="{{ route('coins.index') }}" method="GET" class="mr-2">
                            <div class="input-group" style="width: 200px;">
                                <input type="text" name="search" class="form-control float-right" placeholder="Search" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <a href="{{ route('coins.export', ['search' => request('search')]) }}" class="btn btn-sm btn-info mr-1" title="Export Excel">
                            <i class="fas fa-file-export"></i> Export
                        </a>
                        <button type="button" class="btn btn-sm btn-success mr-1" data-toggle="modal" data-target="#importModal" title="Import Excel">
                            <i class="fas fa-file-excel"></i> Import
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm table-hover table-striped text-nowrap">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Order</th>
                                <th>Container</th>
                                <th>Customer</th>
                                <th>SO</th>
                                <th class="text-center">ATD</th>
                                <th>Stasiun Asal</th>
                                <th>Generic Status</th>
                                <th class="text-center" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coins as $coin)
                            <tr>
                                <td class="align-middle text-center">{{ $coins->firstItem() + $loop->index }}</td>
                                <td class="align-middle">{{ $coin->order_number }}</td>
                                <td class="align-middle">{{ $coin->container }}</td>
                                <td class="align-middle">{{ $coin->customer }}</td>
                                <td class="align-middle">{{ $coin->so }}</td>
                                <td class="align-middle text-center">{{ $coin->atd ? $coin->atd->format('d-m-Y') : '-' }}</td>
                                <td class="align-middle">{{ $coin->stasiun_asal }}</td>
                                <td class="align-middle">
                                    <!-- Simple logic for display, can be customized -->
                                    <span class="badge badge-info">{{ $coin->service }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('coins.show', $coin->id) }}">
                                                <i class="fas fa-eye mr-2 text-info"></i> View
                                            </a>
                                            <a class="dropdown-item" href="{{ route('coins.edit', $coin->id) }}">
                                                <i class="fas fa-edit mr-2 text-primary"></i> Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('coins.destroy', $coin->id) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash mr-2"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-coins fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No Coin Data found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    {{ $coins->withQueryString()->links() }}
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Coin Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('coins.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Choose Excel File</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="exampleInputFile" name="file" required>
                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                            </div>
                            <div class="input-group-append">
                                <a href="{{ route('coins.template') }}" class="btn btn-outline-light text-dark bg-white">
                                    <i class="fas fa-download"></i> Template
                                </a>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Ensure your Excel properties match the required template.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Import</button>
                    <button type="button" class="btn btn-default float-right" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Custom File Input Label
        bsCustomFileInput.init();

        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
