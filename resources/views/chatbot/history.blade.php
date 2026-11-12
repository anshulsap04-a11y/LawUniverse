@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : 'layouts.user-portal', ['title' => 'AI Chatbot History - Law Universe'])

@section('content')
<div class="container-fluid py-3">
    <div class="content-card p-4">

        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history text-primary me-2"></i> AI Chatbot Conversation History
                </h4>
                <span class="text-muted small">Previous legal inquiries, generated drafts, and research logs.</span>
            </div>
            <a href="{{ route('chatbot.ask') }}" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-plus-circle me-1"></i> New Query
            </a>
        </div>

        <!-- Filter bar matching Spec Screenshot (Page 39) -->
        <div class="row g-2 align-items-center mb-4">
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm" placeholder="Search here ...">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-spec-refresh btn-sm">Refresh</button>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-spec-trash btn-sm">Multi-Remove</button>
            </div>
            <div class="col-auto d-flex align-items-center gap-2">
                <span class="small text-muted fw-semibold">Filter by Add Date</span>
                <input type="date" class="form-control form-control-sm">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-custom-header mb-0">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" class="form-check-input"></th>
                        <th width="50">#</th>
                        <th>Question / Prompt</th>
                        <th>Date</th>
                        <th>Model</th>
                        <th>GPT Engine</th>
                        <th>Vendor</th>
                        <th>Charge</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>1</td>
                        <td class="fw-semibold">What are essential grounds for Anticipatory Bail under Section 438 CrPC?</td>
                        <td class="small text-muted">12 Nov 2026</td>
                        <td><span class="badge bg-secondary-subtle text-dark">GPT-4 Legal</span></td>
                        <td>OpenAI GPT-4o</td>
                        <td>Direct API</td>
                        <td class="small text-success">Free Tier</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary py-0">View</button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>2</td>
                        <td class="fw-semibold">Summarize Kesavananda Bharati Sripadagalvaru v. State of Kerala (1973)</td>
                        <td class="small text-muted">11 Nov 2026</td>
                        <td><span class="badge bg-secondary-subtle text-dark">GPT-4 Legal</span></td>
                        <td>OpenAI GPT-4o</td>
                        <td>Direct API</td>
                        <td class="small text-success">Free Tier</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary py-0">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

