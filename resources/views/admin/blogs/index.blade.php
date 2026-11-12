@extends('layouts.admin', ['title' => 'Manage Blogs - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-journal-richtext text-success me-2"></i> Legal Blog & Editorial</h4>
        <p class="text-muted small mb-0">Publish legal commentaries, case analyses, and constitutional essays.</p>
    </div>
    <div>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-warning fw-semibold shadow-sm">
            <i class="bi bi-pencil-square me-1"></i> Compose Article
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Article Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Views</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blogs as $b)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $b->title }}</div>
                            <small class="text-muted">{{ Str::limit($b->summary, 80) }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $b->category->name ?? 'Legal Insights' }}</span></td>
                        <td>{{ $b->author->full_name ?? 'Editorial Team' }}</td>
                        <td><i class="bi bi-eye me-1 text-muted"></i> {{ $b->views_count }}</td>
                        <td>
                            <span class="badge bg-{{ $b->status === 'published' ? 'success' : 'secondary' }}">
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                        <td>{{ $b->created_at->format('d M, Y') }}</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('public.blogs.show', $b->slug) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Preview Public Page">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <form action="{{ route('admin.blogs.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this blog post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No blog articles found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($blogs->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $blogs->links() }}</div>
    @endif
</div>
@endsection
