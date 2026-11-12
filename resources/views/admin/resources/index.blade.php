@extends('layouts.admin', ['title' => 'Manage Resources - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-folder2-open text-primary me-2"></i> Resource Library</h4>
        <p class="text-muted small mb-0">Browse, filter, and manage all published legal acts, documents, and reference materials.</p>
    </div>
    <div>
        <a href="{{ route('admin.resources.create') }}" class="btn btn-warning fw-semibold shadow-sm">
            <i class="bi bi-cloud-arrow-up me-1"></i> Upload New Resource
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.resources.index') }}" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by title, author..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.resources.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Title</th>
                    <th>Category</th>
                    <th>Author / Year</th>
                    <th>Access</th>
                    <th>Stats</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resources as $res)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $res->title }}</div>
                            <small class="text-muted">{{ strtoupper($res->resource_type) }} &bull; {{ $res->language }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $res->category->name ?? 'General' }}</span></td>
                        <td>
                            <div>{{ $res->author ?? 'Law Universe Editorial' }}</div>
                            <small class="text-muted">{{ $res->year }}</small>
                        </td>
                        <td>
                            @if($res->is_free || $res->price <= 0)
                                <span class="badge bg-success-subtle text-success border border-success">FREE</span>
                            @else
                                <span class="badge bg-warning-subtle text-dark border border-warning">&#8377; {{ number_format($res->price, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            <small class="d-block text-muted"><i class="bi bi-download me-1"></i> {{ $res->downloads_count }} DLs</small>
                            <small class="d-block text-muted"><i class="bi bi-eye me-1"></i> {{ $res->views_count }} views</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $res->status === 'published' ? 'success' : ($res->status === 'draft' ? 'secondary' : 'dark') }}">
                                {{ ucfirst($res->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <form action="{{ route('admin.resources.clone', $res->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Clone Resource">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.resources.destroy', $res->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this resource?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Trash">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No legal resources found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($resources->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $resources->links() }}
        </div>
    @endif
</div>
@endsection
