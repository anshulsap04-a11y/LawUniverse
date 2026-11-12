@extends('layouts.user-portal', ['title' => 'Legal Blog - Law Universe'])

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">Legal Commentary & Insights</h3>
    <p class="text-muted small">Articles on constitutional jurisprudence, criminal law updates, and court commentaries.</p>
</div>

<div class="row g-4">
    @forelse($blogs as $b)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <span class="badge bg-light text-primary border mb-2">{{ $b->category->name ?? 'Analysis' }}</span>
                    <h5 class="fw-bold text-dark mb-2">
                        <a href="{{ route('public.blogs.show', $b->slug) }}" class="text-decoration-none text-dark hover-primary">{{ $b->title }}</a>
                    </h5>
                    <p class="text-muted small mb-3">{{ Str::limit($b->summary, 120) }}</p>
                    <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top">
                        <span><i class="bi bi-person me-1"></i> {{ $b->author->full_name ?? 'Editorial' }}</span>
                        <span>{{ $b->created_at->format('d M, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card border-0 shadow-sm p-4 text-center text-muted">No blog articles published.</div></div>
    @endforelse
</div>

<div class="mt-4">{{ $blogs->links() }}</div>
@endsection

