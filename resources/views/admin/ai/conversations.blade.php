@extends('layouts.admin', ['title' => 'AI Conversation History - Law Universe'])

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-clock-history text-primary me-2"></i> User AI Chatbot Sessions</h4>
    <p class="text-muted small mb-0">Audit history of legal inquiries, generated citations, and user conversation sessions.</p>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">User</th>
                    <th>Topic / First Prompt</th>
                    <th>Messages</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conversations as $c)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $c->user->full_name ?? 'User' }}</div>
                            <small class="text-muted">{{ $c->user->email ?? '' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-primary">{{ $c->title }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $c->messages->count() }} messages</span></td>
                        <td>{{ $c->created_at->format('d M, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No AI conversations logged.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($conversations->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $conversations->links() }}</div>
    @endif
</div>
@endsection
