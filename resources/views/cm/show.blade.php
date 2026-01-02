@extends('layouts.app')

@section('title', 'View CM Data')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">CM Data Details - {{ $cm->container }}</h3>
                <div class="card-tools">
                    <a href="{{ route('cms.index') }}" class="btn btn-sm btn-default">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">PPCW</th>
                                <td>{{ $cm->ppcw }}</td>
                            </tr>
                            <tr>
                                <th>Container</th>
                                <td>{{ $cm->container }}</td>
                            </tr>
                            <tr>
                                <th>Seal</th>
                                <td>{{ $cm->seal ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Shipper</th>
                                <td>{{ $cm->shipper }}</td>
                            </tr>
                            <tr>
                                <th>Consignee</th>
                                <td>{{ $cm->consignee }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
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
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">Commodity</th>
                                <td>{{ $cm->commodity ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Size</th>
                                <td>{{ $cm->size }}</td>
                            </tr>
                            <tr>
                                <th>Berat</th>
                                <td>{{ $cm->berat }}</td>
                            </tr>
                            <tr>
                                <th>CM</th>
                                <td>{{ $cm->cm }}</td>
                            </tr>
                            <tr>
                                <th>ATD</th>
                                <td>{{ $cm->atd ? $cm->atd->format('d F Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $cm->keterangan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('cms.edit', $cm->id) }}" class="btn btn-info">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('cms.destroy', $cm->id) }}" method="POST" class="d-inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger float-right">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
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
