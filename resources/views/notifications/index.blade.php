@extends('layouts.app')

@section('title', 'Notifications - MES')
@section('page-title', 'Notifications')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Notifications</h5>
        <button class="btn btn-sm btn-outline-primary" onclick="markAllRead()">Mark All Read</button>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @forelse($notifications as $notification)
                <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                @if(!$notification->read_at)
                                    <span class="badge bg-primary me-1">New</span>
                                @endif
                                {{ $notification->title }}
                            </h6>
                            <p class="mb-1">{{ $notification->message }}</p>
                            <small class="text-muted">
                                <span class="badge bg-{{ $notification->priority === 'critical' ? 'danger' : ($notification->priority === 'high' ? 'warning' : 'secondary') }}">
                                    {{ $notification->priority }}
                                </span>
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                        @if(!$notification->read_at)
                            <button class="btn btn-sm btn-outline-success" onclick="markRead({{ $notification->id }})">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-muted">No notifications</div>
            @endforelse
        </div>
    </div>
    <div class="mt-3 px-3">{{ $notifications->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function markRead(id) {
    $.post('/notifications/' + id + '/read', function(response) {
        location.reload();
    }).fail(function(xhr) {
        console.error('Mark read failed:', xhr.responseText);
    });
}

function markAllRead() {
    $.post('/notifications/read-all', function(response) {
        location.reload();
    }).fail(function(xhr) {
        console.error('Mark all read failed:', xhr.responseText);
    });
}
</script>
@endpush
