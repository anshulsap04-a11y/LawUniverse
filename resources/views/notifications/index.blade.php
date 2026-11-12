@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : 'layouts.user-portal', ['title' => 'Notifications - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-bell-fill text-warning me-2"></i> Notification Inbox</h4>
        <p class="text-muted small mb-0">Stay updated with judicial announcements, exam schedules, and account notices.</p>
    </div>
    <form action="{{ route('notifications.read_all') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-check-all me-1"></i> Mark All as Read
        </button>
    </form>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="list-group list-group-flush">
        @forelse($notifications as $item)
            @php $n = $item->notification; @endphp
            <div class="list-group-item p-3 {{ $item->is_read ? '' : 'bg-light border-start border-4 border-warning' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="me-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h6 class="fw-bold mb-0 text-dark">{{ $n->title }}</h6>
                            <span class="badge bg-{{ $n->type === 'alert' ? 'danger' : ($n->type === 'announcement' ? 'info' : 'primary') }}">
                                {{ ucfirst($n->type) }}
                            </span>
                            @if(!$item->is_read)
                                <span class="badge bg-warning text-dark">NEW</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-2">{{ $n->message }}</p>
                        <small class="text-secondary"><i class="bi bi-clock me-1"></i> {{ $item->created_at->diffForHumans() }}</small>
                    </div>

                    <div>
                        @if(!$item->is_read)
                            <form action="{{ route('notifications.read', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    Mark Read
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-5 text-center text-muted">
                <i class="bi bi-bell-slash fs-1 d-block mb-2 text-secondary"></i>
                <h6>Your notification inbox is clean.</h6>
                <p class="small">No alerts or announcements at this time.</p>
            </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
