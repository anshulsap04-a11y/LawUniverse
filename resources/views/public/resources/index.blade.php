@extends('layouts.user-portal', ['title' => 'Resource Library - Law Universe'])

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">Legal Resource Library</h3>
    <p class="text-muted small">Explore textbooks, case briefs, journals, and procedural guides.</p>
</div>

<div class="row g-4">
    @forelse($resources as $r)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-light text-primary border">{{ $r->category->name ?? 'General' }}</span>
                        @if($r->is_free || $r->price <= 0)
                            <span class="badge bg-success-subtle text-success">FREE</span>
                        @else
                            <span class="badge bg-warning-subtle text-dark">&#8377; {{ number_format($r->price, 2) }}</span>
                        @endif
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ $r->title }}</h5>
                    <p class="text-muted small mb-3">{{ Str::limit($r->description, 120) }}</p>
                    <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top">
                        <span><i class="bi bi-person me-1"></i> {{ $r->author ?? 'Law Universe' }}</span>
                        <span><i class="bi bi-eye me-1"></i> {{ $r->views_count }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card border-0 shadow-sm p-4 text-center text-muted">No resources available.</div></div>
    @endforelse
</div>

<div class="mt-4">{{ $resources->links() }}</div>
@endsection

