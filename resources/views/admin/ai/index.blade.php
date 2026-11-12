@extends('layouts.admin', ['title' => 'GPT Engine & AI Management - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-cpu text-primary me-2"></i> Legal AI Assistant & Vendor APIs</h4>
        <p class="text-muted small mb-0">Configure multi-model vendor routing, encryption keys, and monitor enterprise AI token burn.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVendorModal">
        <i class="bi bi-plus-lg me-1"></i> Configure Vendor API
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-primary text-white">
            <h6 class="text-white-50 mb-1">Total Token Burn</h6>
            <h3 class="fw-bold mb-0">{{ number_format($totalTokens) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-danger text-white">
            <h6 class="text-white-50 mb-1">Approx API Cost</h6>
            <h3 class="fw-bold mb-0">&#36; {{ number_format($totalCost, 4) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-dark text-white">
            <h6 class="text-white-50 mb-1">Conversations Logged</h6>
            <h3 class="fw-bold mb-0">{{ number_format($conversationsCount) }}</h3>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Configured AI Providers & Models</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Provider Name</th>
                    <th>Model</th>
                    <th>Endpoint</th>
                    <th>Cost / 1K Tokens</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $v)
                    <tr>
                        <td class="ps-3 fw-bold text-dark">{{ $v->name }}</td>
                        <td><code>{{ $v->model_name }}</code></td>
                        <td><small class="text-muted font-monospace">{{ $v->api_endpoint }}</small></td>
                        <td>&#36; {{ number_format($v->cost_per_1k_tokens, 5) }}</td>
                        <td>
                            @if($v->is_active)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Active Engine</span>
                            @else
                                <span class="badge bg-secondary">Standby</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            @if(!$v->is_active)
                                <form action="{{ route('admin.ai.vendors.toggle', $v->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">Set Active</button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-success disabled">Active</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No AI vendors configured.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.ai.vendors.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Configure AI Engine API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Vendor Label <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. OpenAI GPT-4o Enterprise">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">API Endpoint <span class="text-danger">*</span></label>
                    <input type="url" name="api_endpoint" class="form-control" required value="https://api.openai.com/v1/chat/completions">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Model Identifier <span class="text-danger">*</span></label>
                    <input type="text" name="model_name" class="form-control" required placeholder="gpt-4o">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Secret API Key (Encrypted in DB) <span class="text-danger">*</span></label>
                    <input type="password" name="api_key" class="form-control" required placeholder="sk-...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Cost / 1K Tokens (USD)</label>
                    <input type="number" step="0.0001" name="cost_per_1k_tokens" class="form-control" required value="0.005">
                </div>
                <div class="col-md-6 d-flex align-items-center pt-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="v_active" value="1">
                        <label class="form-check-label fw-semibold" for="v_active">Set as Active Engine</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save API Config</button>
            </div>
        </form>
    </div>
</div>
@endsection
