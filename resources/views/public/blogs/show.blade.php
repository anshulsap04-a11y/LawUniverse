@extends('layouts.user-portal', ['title' => $blog->title])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="mb-3">
                    <span class="badge bg-light text-primary border me-2">{{ $blog->category->name ?? 'Legal Analysis' }}</span>
                    <span class="text-muted small"><i class="bi bi-clock me-1"></i> {{ $blog->created_at->format('d M, Y') }} &bull; {{ $blog->views_count }} views</span>
                </div>
                <h1 class="fw-bold text-dark mb-4">{{ $blog->title }}</h1>
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; font-weight: bold;">
                        {{ strtoupper(substr($blog->author->first_name ?? 'L', 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark">{{ $blog->author->full_name ?? 'Law Universe Editorial Board' }}</div>
                        <small class="text-muted">{{ $blog->author->role_name ?? 'Author' }}</small>
                    </div>
                </div>

                @if($blog->summary)
                    <div class="p-3 bg-light rounded-3 fst-italic mb-4 border-start border-4 border-primary">
                        {{ $blog->summary }}
                    </div>
                @endif

                <div class="article-content fs-6 lh-lg text-dark" style="white-space: pre-line;">
                    {!! nl2br(e($blog->content)) !!}
                </div>

                @if($blog->tags)
                    <div class="mt-5 pt-3 border-top">
                        <small class="text-muted fw-bold me-2">TAGS:</small>
                        @foreach(explode(',', $blog->tags) as $tag)
                            <span class="badge bg-light text-dark border me-1">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
