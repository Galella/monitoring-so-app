@extends('layouts.app')

@section('title', 'Monitoring SO & Update SO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'not_submitted' || !$status ? 'active' : '' }}" href="{{ route('monitoring-so.index', ['status' => 'not_submitted']) }}">
                                Not Submitted <span class="badge badge-warning float-right ml-2">{{ $counts['not_submitted'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'system' ? 'active' : '' }}" href="{{ route('monitoring-so.index', ['status' => 'system']) }}">
                                Submitted Sistem <span class="badge badge-success float-right ml-2">{{ $counts['system'] }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'manual' ? 'active' : '' }}" href="{{ route('monitoring-so.index', ['status' => 'manual']) }}">
                                Submitted Manual <span class="badge badge-info float-right ml-2">{{ $counts['manual'] }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    
                    <!-- Bulk Actions -->
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                        <form action="{{ route('monitoring-so.index') }}" method="GET" class="form-inline">
                            <input type="hidden" name="status" value="{{ $status }}">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Search Container/CM/SO" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                            <a href="{{ route('monitoring-so.export', ['status' => $status, 'search' => request('search')]) }}" class="btn btn-success btn-sm ml-2">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>

                        <button type="button" class="btn btn-info btn-sm" id="btn-bulk-manual" disabled>
                            <i class="fas fa-check-double"></i> Set Selected to Manual
                        </button>
                    </div>

                    <form id="bulk-update-form" action="{{ route('monitoring-so.bulk-update') }}" method="POST" style="display: none;">
                        @csrf
                        <div id="bulk-inputs"></div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="40" class="text-center">
                                        <input type="checkbox" id="select-all">
                                    </th>
                                    <th>Container</th>
                                    <th>CM No</th>
                                    <th>CM Seal</th>
                                    <th>Customer</th>
                                    <th>PO Number</th>
                                    <th>Status SO</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items->groupBy('po') as $po => $groupItems)
                                    <tr class="bg-light">
                                        <td colspan="8">
                                            <strong>PO Number: {{ $po ?: 'No PO' }}</strong> 
                                            <span class="badge badge-secondary ml-2">{{ $groupItems->count() }} items</span>
                                        </td>
                                    </tr>
                                    @foreach($groupItems as $item)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="select-item" value="{{ $item->id }}">
                                        </td>
                                        <td>{{ $item->container }}</td>
                                        <td>{{ $item->cm }}</td>
                                        <td>{{ $item->cm_seal }}</td>
                                        <td>{{ $item->customer }}</td>
                                        <td>{{ $item->po }}</td>
                                        <td>
                                            @if(preg_match('/^[0-9]+$/', $item->so))
                                                <span class="badge badge-success">Sistem</span>
                                            @elseif(str_contains($item->so, 'Manual'))
                                                <span class="badge badge-info">Manual</span>
                                            @else
                                                <span class="badge badge-warning">Not Submitted</span>
                                            @endif
                                        </td>
                                        <td>
                                                <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#editSoModal{{ $item->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editSoModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editSoModalLabel{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('monitoring-so.update', $item->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editSoModalLabel{{ $item->id }}">Update SO for {{ $item->container }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="so">SO Value</label>
                                                                    <input type="text" class="form-control" name="so" value="{{ ($item->so == 'Not Submitted' || empty($item->so)) ? 'Manual' : $item->so }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="submit_so">Submit Date</label>
                                                                    <input type="date" class="form-control" name="submit_so" value="{{ $item->submit_so ? \Carbon\Carbon::parse($item->submit_so)->format('Y-m-d') : date('Y-m-d') }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No matched records found for this category.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $items->links() }}
                    </div>

                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<div class="modal fade" id="bulkConfirmModal" tabindex="-1" role="dialog" aria-labelledby="bulkConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkConfirmModalLabel">Confirm Bulk Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to update <span id="selected-count" class="font-weight-bold">0</span> records?</p>
                <p>These records will be set to:</p>
                <ul>
                    <li><strong>SO:</strong> Manual</li>
                    <li><strong>Submit Date:</strong> {{ date('Y-m-d') }}</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-bulk-update">Yes, Update Records</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Toggle Select All
        $('#select-all').click(function() {
            var checked = this.checked;
            $('.select-item').each(function() {
                this.checked = checked;
            });
            toggleBulkButton();
        });

        // Individual selection
        $('.select-item').click(function() {
            if ($('.select-item:checked').length == $('.select-item').length) {
                $('#select-all').prop('checked', true);
            } else {
                $('#select-all').prop('checked', false);
            }
            toggleBulkButton();
        });

        function toggleBulkButton() {
            if ($('.select-item:checked').length > 0) {
                $('#btn-bulk-manual').removeAttr('disabled');
            } else {
                $('#btn-bulk-manual').attr('disabled', 'disabled');
            }
        }

        // Handle Bulk Button Click - Show Modal
        $('#btn-bulk-manual').click(function() {
            var count = $('.select-item:checked').length;
            $('#selected-count').text(count);
            $('#bulkConfirmModal').modal('show');
        });

        // Handle Confirmation Click
        $('#confirm-bulk-update').click(function() {
            var form = $('#bulk-update-form');
            $('#bulk-inputs').empty();

            $('.select-item:checked').each(function() {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'ids[]',
                    value: $(this).val()
                }).appendTo('#bulk-inputs');
            });

            form.submit();
        });
    });
</script>
@endpush
@endsection
