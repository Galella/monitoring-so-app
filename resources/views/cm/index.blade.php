@extends('layouts.app')

@section('title', 'CM Data Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">CM Data List</h3>
                    <div class="card-tools d-flex">
                        <form action="{{ route('cms.index') }}" method="GET" class="mr-2">
                            <div class="input-group" style="width: 200px;">
                                <input type="text" name="search" class="form-control float-right" placeholder="Search" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <a href="{{ route('cms.export', ['search' => request('search')]) }}" class="btn btn-sm btn-info mr-1" title="Export Excel">
                            <i class="fas fa-file-export"></i> Export
                        </a>
                        <button type="button" class="btn btn-sm btn-success mr-1" data-toggle="modal" data-target="#importModal" title="Import Excel">
                            <i class="fas fa-file-excel"></i> Import
                        </button>
                        <!-- <a href="{{ route('cms.create') }}" class="btn btn-sm btn-primary" title="Add New Data">
                            <i class="fas fa-plus"></i> Add
                        </a> -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">

                    

                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <table class="table table-sm table-hover table-striped text-nowrap">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>PPCW</th>
                                <th>Container</th>
                                <th>Shipper</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Size</th>
                                <th>CM</th>
                                <th class="text-center">ATD</th>
                                <th class="text-center" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cms as $cm)
                            <tr>
                                <td class="align-middle text-center">{{ $cms->firstItem() + $loop->index }}</td>
                                <td class="align-middle">{{ $cm->ppcw }}</td>
                                <td class="align-middle">{{ $cm->container }}</td>
                                <td class="align-middle">{{ $cm->shipper }}</td>
                                <td class="align-middle text-center">
                                    @php
                                        $statusClass = match(strtolower($cm->status)) {
                                            'completed', 'done', 'finish' => 'success',
                                            'pending', 'waiting' => 'warning',
                                            'process', 'ongoing' => 'info',
                                            'cancel', 'rejected' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }}">{{ $cm->status }}</span>
                                </td>
                                <td class="align-middle text-center">{{ $cm->size }}</td>
                                <td class="align-middle">{{ $cm->cm }}</td>
                                <td class="align-middle text-center">{{ $cm->atd ? $cm->atd->format('d-m-Y') : '-' }}</td>
                                <td class="align-middle text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('cms.show', $cm->id) }}">
                                                <i class="fas fa-eye mr-2 text-info"></i> View
                                            </a>
                                            <a class="dropdown-item" href="{{ route('cms.edit', $cm->id) }}">
                                                <i class="fas fa-edit mr-2 text-primary"></i> Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('cms.destroy', $cm->id) }}" method="POST" class="delete-form">
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
                                <td colspan="10" class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No CM Data found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    {{ $cms->withQueryString()->links() }}
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
                <h5 class="modal-title" id="importModalLabel">Import CM Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('cms.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="exampleInputFile" name="file">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                      <div class="input-group-append">
                        <a href="{{ route('cms.template') }}" class="btn btn-outline-light text-dark bg-white">
                            <i class="fas fa-download"></i> Template
                        </a>
                      </div>
                    </div>
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
