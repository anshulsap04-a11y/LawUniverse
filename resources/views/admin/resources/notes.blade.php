@extends('layouts.admin', ['title' => 'Legal Notes - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-journal-text text-info me-2"></i> Notes</h4>
        <p class="text-muted small mb-0">Create, organize, and manage personal and shared notes related to legal topics.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
        <i class="bi bi-plus-lg me-1"></i> Create Note
    </button>
</div>

<!-- Filters Panel matching PDF Page 15 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.resources.notes') }}" id="filterForm" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search Notes by title, subject..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.resources.notes') }}" class="btn btn-outline-secondary" title="Refresh"><i class="bi bi-arrow-counterclockwise"></i> Refresh</a>
            </div>

            <!-- Checkbox matching PDF Page 15 Item 7 -->
            <div class="col-12 pt-2 border-top mt-2 d-flex gap-4 small">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="my_data" id="my_data" value="1" {{ request('my_data') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                    <label class="form-check-label fw-semibold" for="my_data">My Data (Uploaded by Me)</label>
                </div>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.resources.notes.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiTrashBtn" onclick="return confirm('Delete selected notes?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi-Trash
            </button>
            <span class="text-muted small">Showing {{ $notes->total() }} Notes</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Visibility</th>
                        <th>Author</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes as $note)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $note->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $note->title }}</div>
                                <small class="text-muted">{{ Str::limit(strip_tags($note->content), 80) }}</small>
                            </td>
                            <td><span class="badge bg-light text-primary border">{{ $note->subject ?? 'General' }}</span></td>
                            <td>
                                @if($note->is_shared)
                                    <span class="badge bg-success-subtle text-success">Shared</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Private</span>
                                @endif
                            </td>
                            <td>{{ $note->user->name ?? 'Admin' }}</td>
                            <td>{{ $note->created_at->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No notes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notes->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $notes->links() }}</div>
        @endif
    </div>
</form>

<!-- Modal Add -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.resources.notes.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create Legal Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Note Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Fundamental Rights Doctrine Notes">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="e.g. Constitutional Law">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                    <textarea name="content" rows="6" class="form-control" required></textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_shared" id="is_shared" value="1" checked>
                        <label class="form-check-label fw-semibold" for="is_shared">Make visible to platform users</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Note</button>
            </div>
        </form>
    </div>
</div>

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
