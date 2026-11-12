@extends('layouts.admin', ['title' => 'Landmark Judgments - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-bank text-info me-2"></i> Landmark Judgments</h4>
        <p class="text-muted small mb-0">Authoritative Supreme Court and High Court precedents, constitutional benches, and ratio decidendi.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJudgmentModal">
        <i class="bi bi-plus-lg me-1"></i> Add Landmark Judgment
    </button>
</div>

<!-- Filters Panel matching PDF Page 13 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.resources.landmark_judgments') }}" id="filterForm" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by Case Name, Citation..." value="{{ request('search') }}">
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
                <input type="number" name="year" class="form-control" placeholder="Year" value="{{ request('year') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.resources.landmark_judgments') }}" class="btn btn-outline-secondary" title="Refresh"><i class="bi bi-arrow-counterclockwise"></i> Clear</a>
            </div>

            <!-- Checkbox matching PDF Page 13 Item 2, 6 -->
            <div class="col-12 pt-2 border-top mt-2 d-flex gap-4 small">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="my_data" id="my_data" value="1" {{ request('my_data') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                    <label class="form-check-label fw-semibold" for="my_data">My Data (Uploaded by Me)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_free" id="is_free" value="1" {{ request('is_free') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                    <label class="form-check-label fw-semibold" for="is_free">Offered Free of Cost</label>
                </div>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.resources.landmark_judgments.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiTrashBtn" onclick="return confirm('Delete selected judgments?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi-Trash
            </button>
            <span class="text-muted small">Showing {{ $judgments->total() }} Landmark Precedents</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Case Name</th>
                        <th>Citation & Court</th>
                        <th>Date & Bench</th>
                        <th>Legal Principle / Ratio</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($judgments as $j)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $j->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $j->case_name }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-secondary">{{ $j->citation }}</div>
                                <small class="text-muted">{{ $j->court }}</small>
                            </td>
                            <td>
                                <div>{{ $j->judgment_date ? \Carbon\Carbon::parse($j->judgment_date)->format('d M, Y') : 'N/A' }}</div>
                                <small class="text-muted">{{ $j->bench ?? 'Bench' }}</small>
                            </td>
                            <td><div class="text-truncate" style="max-width: 340px;">{{ $j->legal_principle }}</div></td>
                            <td class="text-end pe-3">
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#jdgModal_{{ $j->id }}">Details</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No Landmark Judgments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($judgments->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $judgments->links() }}</div>
        @endif
    </div>
</form>

<!-- Modal Add -->
<div class="modal fade" id="addJudgmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.resources.landmark_judgments.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Record Landmark Precedent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Case Title <span class="text-danger">*</span></label>
                    <input type="text" name="case_name" class="form-control" required placeholder="e.g. Kesavananda Bharati v. State of Kerala">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Citation <span class="text-danger">*</span></label>
                    <input type="text" name="citation" class="form-control" required placeholder="(1973) 4 SCC 225">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Court Name</label>
                    <input type="text" name="court" class="form-control" value="Supreme Court of India">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Judgment Date <span class="text-danger">*</span></label>
                    <input type="date" name="judgment_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Bench</label>
                    <input type="text" name="bench" class="form-control" placeholder="13 Judges Bench">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Legal Principle / Held <span class="text-danger">*</span></label>
                    <textarea name="legal_principle" rows="3" class="form-control" required></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Case Summary</label>
                    <textarea name="case_summary" rows="3" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Judgment</button>
            </div>
        </form>
    </div>
</div>

@foreach($judgments as $j)
    <div class="modal fade" id="jdgModal_{{ $j->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark">{{ $j->case_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2"><strong>Citation:</strong> {{ $j->citation }} &bull; {{ $j->court }}</div>
                    <div class="mb-3"><strong>Bench:</strong> {{ $j->bench }}</div>
                    <div class="p-3 bg-light rounded border mb-3">
                        <strong class="text-primary d-block mb-1">Ratio Decidendi / Legal Principle:</strong>
                        <p class="mb-0">{{ $j->legal_principle }}</p>
                    </div>
                    <p class="text-muted small">{{ $j->case_summary }}</p>
                </div>
            </div>
        </div>
    </div>
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
