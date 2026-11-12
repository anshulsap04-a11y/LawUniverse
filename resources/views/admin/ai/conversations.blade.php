@extends('layouts.admin', ['title' => 'AI Chatbot History - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-clock-history text-primary me-2"></i> AI Chatbot History</h4>
        <p class="text-muted small mb-0">Logs and stores past interactions between users and the AI chatbot (Page 38).</p>
    </div>
    <a href="{{ route('admin.ai.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to GPT Engines
    </a>
</div>

<!-- Filters Panel matching PDF Page 38 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.ai.conversations') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search specific conversation topics..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="Filter by Date">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.ai.conversations') }}" class="btn btn-outline-secondary" title="Clear"><i class="bi bi-arrow-counterclockwise"></i> Clear</a>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.ai.conversations.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiRemoveBtn" onclick="return confirm('Remove selected conversation logs?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi Remove
            </button>
            <span class="text-muted small">Showing {{ $conversations->total() }} User Chat Sessions</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>User</th>
                        <th>Conversation Topic</th>
                        <th>Messages</th>
                        <th>Context</th>
                        <th>Last Activity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conversations as $c)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $c->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $c->user->name ?? 'User' }}</div>
                                <small class="text-muted">{{ $c->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-primary">{{ $c->title }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $c->messages->count() }} messages</span></td>
                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $c->context_type }}</span></td>
                            <td>{{ $c->created_at->format('d M, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No conversation histories logged.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($conversations->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $conversations->links() }}</div>
        @endif
    </div>
</form>

<script>
function toggleSelectAll(master) {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = master.checked);
    checkSelection();
}
function checkSelection() {
    const checked = document.querySelectorAll('.item-checkbox:checked').length;
    document.getElementById('multiRemoveBtn').disabled = (checked === 0);
}
</script>
@endsection
