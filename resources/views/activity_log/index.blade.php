@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Logs</h3>
                    <div class="card-tools">
                        <form action="{{ route('activity-logs.index') }}" method="GET" class="form-inline">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Search Log/User" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Event</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Changes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $activity->causer->name ?? 'System' }}</td>
                                    <td><span class="badge badge-{{ $activity->event == 'created' ? 'success' : ($activity->event == 'updated' ? 'warning' : 'danger') }}">{{ $activity->event }}</span></td>
                                    <td>{{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</td>
                                    <td>{{ $activity->description }}</td>
                                    <td>
                                        @if($activity->properties->has('attributes'))
                                            <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#changesModal{{ $activity->id }}">
                                                View Changes
                                            </button>

                                            <!-- Changes Modal -->
                                            <div class="modal fade" id="changesModal{{ $activity->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Changes for #{{ $activity->id }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th>Attribute</th>
                                                                            <th>Old Value</th>
                                                                            <th>New Value</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($activity->properties['attributes'] ?? [] as $key => $newValue)
                                                                            <tr>
                                                                                <td class="font-weight-bold">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                                                                <td class="text-danger">
                                                                                    {{ $activity->properties['old'][$key] ?? '-' }}
                                                                                </td>
                                                                                <td class="text-success font-weight-bold">
                                                                                    {{ $newValue }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No details</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $activities->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
