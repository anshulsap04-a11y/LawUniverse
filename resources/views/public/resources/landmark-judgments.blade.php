@extends('layouts.user-portal', ['title' => 'Landmark Judgments - Law Universe'])

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">Landmark Judgments of India</h3>
    <p class="text-muted small">Constitutional bench rulings, ratio decidendi, and Supreme Court holdings.</p>
</div>

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('public.landmark-judgments') }}" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search case title or citation..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Search</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    @forelse($judgments as $j)
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary-subtle text-primary">{{ $j->legal_topic ?? 'Constitutional Law' }}</span>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($j->judgment_date)->format('Y') }}</small>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $j->case_title }}</h5>
                    <div class="text-secondary small fw-semibold mb-3">{{ $j->citation }} &bull; {{ $j->court_name }}</div>
                    <div class="p-3 bg-light rounded-2 mb-3">
                        <strong class="d-block text-dark small">Held:</strong>
                        <p class="text-muted small mb-0">{{ $j->held }}</p>
                    </div>
                    <p class="text-muted small mb-0">{{ Str::limit($j->summary, 160) }}</p>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card border-0 shadow-sm p-4 text-center text-muted">No judgments found.</div></div>
    @endforelse
</div>

<div class="mt-4">{{ $judgments->links() }}</div>
@endsection
