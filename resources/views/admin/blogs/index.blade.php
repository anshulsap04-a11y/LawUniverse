@extends('layouts.admin', ['title' => 'List Blogs - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-journal-richtext text-success me-2"></i> List Blogs</h4>
        <p class="text-muted small mb-0">Manage published articles, commentary, sharing, and editorial curation.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.blogs.statistics') }}" class="btn btn-outline-success fw-semibold">
            <i class="bi bi-graph-up me-1"></i> Blogs Statistics
        </a>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-success fw-semibold shadow-sm">
            <i class="bi bi-pencil-square me-1"></i> Compose Blogs
        </a>
    </div>
</div>

<!-- Filters Panel matching PDF Page 28 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.blogs.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by blog title..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="Filter by Date">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary" title="Refresh"><i class="bi bi-arrow-counterclockwise"></i> Refresh</a>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.blogs.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiTrashBtn" onclick="return confirm('Delete selected blogs?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi-Trash
            </button>
            <span class="text-muted small">Showing {{ $blogs->total() }} Blog Posts</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Article Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Pageviews</th>
                        <th>Status</th>
                        <th>Published Date</th>
                        <th class="text-end pe-3">Actions (Page 29)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs as $b)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $b->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $b->title }}</div>
                                <small class="text-muted">{{ Str::limit($b->summary, 80) }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $b->category->name ?? 'Insights' }}</span></td>
                            <td>{{ $b->author->name ?? 'Editorial' }}</td>
                            <td><span class="badge bg-info-subtle text-info"><i class="bi bi-eye me-1"></i> {{ $b->views_count }}</span></td>
                            <td>
                                <span class="badge bg-{{ $b->status === 'published' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($b->status) }}
                                </span>
                            </td>
                            <td>{{ $b->created_at->format('d M, Y') }}</td>
                            <td class="text-end pe-3">
                                <!-- Share Button (Copies Link) -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ route('public.blogs.show', $b->slug) }}'); alert('Blog link copied!')" title="Share">
                                    <i class="bi bi-share"></i> Share
                                </button>
                                <!-- Read Button -->
                                <a href="{{ route('public.blogs.show', $b->slug) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Read">
                                    <i class="bi bi-book-open"></i> Read
                                </a>
                                <!-- Trash -->
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete blog article?')) document.getElementById('trash_{{ $b->id }}').submit()" title="Trash">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No blog articles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($blogs->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $blogs->links() }}</div>
        @endif
    </div>
</form>

@foreach($blogs as $b)
    <form action="{{ route('admin.blogs.destroy', $b->id) }}" method="POST" id="trash_{{ $b->id }}" class="d-none">@csrf @method('DELETE')</form>
@endforeach

<script>
function toggleSelectAll(master) {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = master.checked);
    checkSelection();
}
function checkSelection() {
    const checked = document.querySelectorAll('.item-checkbox:checked').length;
    document.getElementById('multiTrashBtn').disabled = (checked === 0);
}
</script>
@endsection
