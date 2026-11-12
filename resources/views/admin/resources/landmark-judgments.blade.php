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

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Case Title</th>
                    <th>Citation & Court</th>
                    <th>Date & Bench</th>
                    <th>Ratio & Key Holdings</th>
                </tr>
            </thead>
            <tbody>
                @forelse($judgments as $j)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $j->case_title }}</div>
                            <span class="badge bg-light text-primary border">{{ $j->legal_topic ?? 'Constitutional Law' }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-secondary">{{ $j->citation }}</div>
                            <small class="text-muted">{{ $j->court_name }}</small>
                        </td>
                        <td>
                            <div>{{ \Carbon\Carbon::parse($j->judgment_date)->format('d M, Y') }}</div>
                            <small class="text-muted">{{ $j->bench ?? 'Constitution Bench' }}</small>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 380px;"><strong>Held:</strong> {{ $j->held }}</div>
                            <small class="text-muted text-truncate d-block" style="max-width: 380px;">{{ $j->summary }}</small>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No Landmark Judgments registered.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($judgments->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $judgments->links() }}</div>
    @endif
</div>

<!-- Modal -->
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
                    <input type="text" name="case_title" class="form-control" required placeholder="e.g. Kesavananda Bharati v. State of Kerala">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Citation <span class="text-danger">*</span></label>
                    <input type="text" name="citation" class="form-control" required placeholder="(1973) 4 SCC 225">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Court Name <span class="text-danger">*</span></label>
                    <input type="text" name="court_name" class="form-control" required value="Supreme Court of India">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Judgment Date <span class="text-danger">*</span></label>
                    <input type="date" name="judgment_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Bench Size</label>
                    <input type="text" name="bench" class="form-control" placeholder="13 Judges Bench">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Subject Matter / Legal Topic</label>
                    <input type="text" name="legal_topic" class="form-control" placeholder="Basic Structure Doctrine, Amending Power">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Summary of Facts & Arguments <span class="text-danger">*</span></label>
                    <textarea name="summary" rows="3" class="form-control" required></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Ratio Decidendi / Held <span class="text-danger">*</span></label>
                    <textarea name="held" rows="3" class="form-control" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Judgment</button>
            </div>
        </form>
    </div>
</div>
@endsection
