@extends('layouts.user-portal', ['title' => 'Legal Maxims - Law Universe'])

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">Classical Legal Maxims</h3>
    <p class="text-muted small">Latin legal maxims, natural justice principles, and authoritative precedents.</p>
</div>

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('public.legal-maxims') }}" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search by Latin term or English meaning..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Search</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    @forelse($maxims as $m)
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold text-primary fst-italic mb-0">{{ $m->latin_term }}</h5>
                        <span class="badge bg-secondary-subtle text-secondary">{{ $m->origin ?? 'Roman Law' }}</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">{{ $m->english_meaning }}</h6>
                    <p class="text-muted small mb-3">{{ $m->explanation }}</p>
                    @if($m->related_cases)
                        <div class="small text-muted pt-2 border-top">
                            <i class="bi bi-book me-1 text-primary"></i> <strong>Cases:</strong> {{ $m->related_cases }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card border-0 shadow-sm p-4 text-center text-muted">No maxims found.</div></div>
    @endforelse
</div>

<div class="mt-4">{{ $maxims->links() }}</div>
@endsection
