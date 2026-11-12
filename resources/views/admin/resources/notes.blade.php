@extends('layouts.admin', ['title' => 'Legal Notes - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-journal-text text-info me-2"></i> Legal Notes & Summaries</h4>
        <p class="text-muted small mb-0">Faculty notes, student revision digests, and advocate preparatory briefs.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
        <i class="bi bi-plus-lg me-1"></i> Create Legal Note
    </button>
</div>

<div class="row g-3">
    @forelse($notes as $note)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-light text-primary border">{{ $note->subject ?? 'General Law' }}</span>
                        @if($note->is_shared)
                            <span class="badge bg-success-subtle text-success"><i class="bi bi-globe me-1"></i> Public</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-lock me-1"></i> Private</span>
                        @endif
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ $note->title }}</h5>
                    <p class="text-muted small mb-3">{{ Str::limit(strip_tags($note->content), 120) }}</p>
                    <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top">
                        <span><i class="bi bi-person me-1"></i> {{ $note->user->full_name ?? 'Admin' }}</span>
                        <span>{{ $note->created_at->format('d M, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 text-center text-muted">No legal notes found.</div>
        </div>
    @endforelse
</div>

<div class="mt-4">{{ $notes->links() }}</div>

<!-- Modal -->
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
@endsection
