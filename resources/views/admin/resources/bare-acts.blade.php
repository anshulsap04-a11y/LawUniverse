@extends('layouts.admin', ['title' => 'Bare Acts - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-book text-info me-2"></i> Bare Acts Repository</h4>
        <p class="text-muted small mb-0">Central and state statutory legislations, amended acts, and codified codes.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addActModal">
        <i class="bi bi-plus-lg me-1"></i> Add Bare Act
    </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Act No. & Year</th>
                    <th>Title</th>
                    <th>Sections</th>
                    <th>Chapters</th>
                    <th>Enactment Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($acts as $act)
                    <tr>
                        <td class="ps-3 fw-bold text-muted">{{ $act->act_number ?? 'Act' }} of {{ $act->year }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $act->title }}</div>
                            <small class="text-muted">{{ Str::limit($act->description, 80) }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $act->total_sections ?? 'N/A' }}</span></td>
                        <td><span class="badge bg-light text-dark border">{{ $act->total_chapters ?? 'N/A' }}</span></td>
                        <td>{{ $act->enactment_date ? \Carbon\Carbon::parse($act->enactment_date)->format('d M, Y') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No Bare Acts catalogued.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($acts->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $acts->links() }}</div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="addActModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.resources.bare_acts.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Register New Bare Act</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Title of Legislation</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Bharatiya Nagarik Suraksha Sanhita, 2023">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Act No.</label>
                    <input type="text" name="act_number" class="form-control" placeholder="Act 46">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Year</label>
                    <input type="number" name="year" class="form-control" required value="{{ date('Y') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Total Sections</label>
                    <input type="number" name="total_sections" class="form-control" value="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Total Chapters</label>
                    <input type="number" name="total_chapters" class="form-control" value="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Enactment Date</label>
                    <input type="date" name="enactment_date" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description / Preamble</label>
                    <textarea name="description" rows="3" class="form-control"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Codified Statutory Text (Markdown)</label>
                    <textarea name="full_text" rows="6" class="form-control font-monospace"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Bare Act</button>
            </div>
        </form>
    </div>
</div>
@endsection
