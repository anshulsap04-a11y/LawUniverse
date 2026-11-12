@extends('layouts.admin', ['title' => 'Subscriptions - Law Universe'])

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-credit-card text-danger me-2"></i> User Subscriptions</h4>
    <p class="text-muted small mb-0">Monitor active, expired, and cancelled membership plans across roles.</p>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Subscriber</th>
                    <th>Package Plan</th>
                    <th>Starts At</th>
                    <th>Expires At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $s)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $s->user->full_name ?? 'User' }}</div>
                            <small class="text-muted">{{ $s->user->email ?? '' }}</small>
                        </td>
                        <td><span class="badge bg-light text-primary border">{{ $s->package->name ?? 'Standard' }}</span></td>
                        <td>{{ $s->starts_at->format('d M, Y') }}</td>
                        <td>{{ $s->ends_at->format('d M, Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $s->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($s->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No subscriptions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subscriptions->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $subscriptions->links() }}</div>
    @endif
</div>
@endsection
