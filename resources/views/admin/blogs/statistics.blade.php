@extends('layouts.admin', ['title' => 'Blog Statistics - Law Universe'])

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-graph-up text-success me-2"></i> Legal Blog Readership Analytics</h4>
    <p class="text-muted small mb-0">Overview of readership engagement, published pieces, and most popular analyses.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Total Articles</h6>
                    <h3 class="fw-bold mb-0">{{ $totalBlogs }}</h3>
                </div>
                <i class="bi bi-journal-text fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Published</h6>
                    <h3 class="fw-bold mb-0">{{ $publishedBlogs }}</h3>
                </div>
                <i class="bi bi-check2-circle fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-secondary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Drafts</h6>
                    <h3 class="fw-bold mb-0">{{ $draftBlogs }}</h3>
                </div>
                <i class="bi bi-pencil-square fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Total Pageviews</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($totalViews) }}</h3>
                </div>
                <i class="bi bi-eye fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Top 10 Most Read Legal Articles</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Article Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Total Views</th>
                    <th>Published</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topBlogs as $b)
                    <tr>
                        <td class="ps-3 fw-bold text-dark">{{ $b->title }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $b->category->name ?? 'General' }}</span></td>
                        <td>{{ $b->author->full_name ?? 'Editorial' }}</td>
                        <td><span class="badge bg-info-subtle text-info fs-6">{{ number_format($b->views_count) }} views</span></td>
                        <td>{{ $b->published_at ? $b->published_at->format('d M, Y') : 'Draft' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No readership data logged.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

