@extends('layouts.admin', ['title' => 'Legal Maxims - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-feather text-info me-2"></i> Legal Maxims Repository</h4>
        <p class="text-muted small mb-0">Classical Latin legal maxims, authoritative doctrines, and statutory canons.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaximModal">
        <i class="bi bi-plus-lg me-1"></i> Add Legal Maxim
    </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Latin Term</th>
                    <th>English Meaning</th>
                    <th>Origin</th>
                    <th>Explanation & Case Law</th>
                </tr>
            </thead>
            <tbody>
                @forelse($maxims as $m)
                    <tr>
                        <td class="ps-3 fw-bold text-primary fst-italic">{{ $m->latin_term }}</td>
                        <td class="fw-semibold text-dark">{{ $m->english_meaning }}</td>
                        <td><span class="badge bg-secondary-subtle text-secondary">{{ $m->origin ?? 'Roman Law' }}</span></td>
                        <td>
                            <div>{{ Str::limit($m->explanation, 90) }}</div>
                            @if($m->related_cases)
                                <small class="text-muted"><i class="bi bi-book me-1"></i> {{ $m->related_cases }}</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No Legal Maxims found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($maxims->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $maxims->links() }}</div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="addMaximModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.resources.legal_maxims.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Legal Maxim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Latin Term <span class="text-danger">*</span></label>
                    <input type="text" name="latin_term" class="form-control" required placeholder="e.g. Audi alteram partem">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">English Meaning <span class="text-danger">*</span></label>
                    <input type="text" name="english_meaning" class="form-control" required placeholder="e.g. Listen to the other side">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jurisprudential Origin</label>
                    <input type="text" name="origin" class="form-control" placeholder="Roman Jurisprudence / Common Law">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Leading Related Precedents</label>
                    <input type="text" name="related_cases" class="form-control" placeholder="e.g. Maneka Gandhi v. UOI (1978)">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Doctrinal Explanation <span class="text-danger">*</span></label>
                    <textarea name="explanation" rows="4" class="form-control" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Maxim</button>
            </div>
        </form>
    </div>
</div>
@endsection
