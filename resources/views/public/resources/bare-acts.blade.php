@extends('layouts.user-portal', ['title' => 'Bare Acts - Law Universe'])

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">Bare Acts of India</h3>
    <p class="text-muted small">Central & state statutory legislations with codified sections.</p>
</div>

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('public.bare-acts') }}" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search bare acts by title, year..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Search</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    @forelse($acts as $act)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <span class="badge bg-light text-primary border mb-2">{{ $act->act_number ?? 'Act' }} of {{ $act->year }}</span>
                    <h5 class="fw-bold text-dark mb-2">{{ $act->title }}</h5>
                    <p class="text-muted small mb-3">{{ Str::limit($act->description, 140) }}</p>
                    <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top">
                        <span>{{ $act->total_sections ?? 0 }} Sections</span>
                        <span>{{ $act->total_chapters ?? 0 }} Chapters</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card border-0 shadow-sm p-4 text-center text-muted">No acts found.</div></div>
    @endforelse
</div>

<div class="mt-4">{{ $acts->links() }}</div>
@endsection

