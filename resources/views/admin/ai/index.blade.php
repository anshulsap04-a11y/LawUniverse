@extends('layouts.admin', ['title' => 'Manage GPT Engine API - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-cpu text-primary me-2"></i> Manage GPT Engine API</h4>
        <p class="text-muted small mb-0">API engine configuration, model selection, server validation, and status control (Page 39 & 40).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.ai.conversations') }}" class="btn btn-outline-primary fw-semibold">
            <i class="bi bi-clock-history me-1"></i> AI Chatbot History
        </a>
        <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#addVendorModal">
            <i class="bi bi-plus-lg me-1"></i> Add New API Vendor
        </button>
    </div>
</div>

<!-- Filters Panel matching PDF Page 39 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.ai.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search API Engine by name, model..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive / Deactivated</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.ai.index') }}" class="btn btn-outline-secondary" title="Clear"><i class="bi bi-arrow-counterclockwise"></i> Clear</a>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.ai.vendors.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiRemoveBtn" onclick="return confirm('Delete selected API engines?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi Remove
            </button>
            <span class="text-muted small">Showing {{ $vendors->count() }} Configured Engines</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>API Vendor Name</th>
                        <th>Model</th>
                        <th>Base Endpoint</th>
                        <th>Status</th>
                        <th>Validity Date</th>
                        <th class="text-end pe-3">Actions (Page 39)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $v)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $v->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td class="fw-bold text-dark">{{ $v->vendor_name }}</td>
                            <td><code>{{ $v->model_name }}</code></td>
                            <td><small class="text-muted font-monospace">{{ $v->api_base_url }}</small></td>
                            <td>
                                @if($v->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Active</span>
                                @else
                                    <span class="badge bg-secondary">Deactivated</span>
                                @endif
                            </td>
                            <td>{{ $v->validity_date ? \Carbon\Carbon::parse($v->validity_date)->format('d M, Y') : 'Indefinite' }}</td>
                            <td class="text-end pe-3">
                                <!-- 7. Deactivate / Activate Button -->
                                <button type="button" class="btn btn-sm btn-outline-warning text-dark" onclick="document.getElementById('toggle_{{ $v->id }}').submit()">
                                    {{ $v->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <!-- 9. Validate Button -->
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('validate_{{ $v->id }}').submit()" title="Validate through server">
                                    <i class="bi bi-patch-check"></i> Validate
                                </button>
                                <!-- 8. Details Button -->
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#vModal_{{ $v->id }}">
                                    Details
                                </button>
                                <!-- 10. Trash Button -->
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete API engine?')) document.getElementById('trash_{{ $v->id }}').submit()">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No API engines configured.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</form>

@foreach($vendors as $v)
    <form action="{{ route('admin.ai.vendors.toggle', $v->id) }}" method="POST" id="toggle_{{ $v->id }}" class="d-none">@csrf</form>
    <form action="{{ route('admin.ai.vendors.validate', $v->id) }}" method="POST" id="validate_{{ $v->id }}" class="d-none">@csrf</form>
    <form action="{{ route('admin.ai.vendors.destroy', $v->id) }}" method="POST" id="trash_{{ $v->id }}" class="d-none">@csrf @method('DELETE')</form>

    <div class="modal fade" id="vModal_{{ $v->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">API Engine Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between"><span>Vendor:</span> <strong>{{ $v->vendor_name }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Model:</span> <strong>{{ $v->model_name }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Endpoint:</span> <strong>{{ $v->api_base_url }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Status:</span> <strong>{{ $v->is_active ? 'Active Engine' : 'Deactivated' }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Admin Contact:</span> <strong>{{ $v->email_address ?? 'N/A' }}</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Add (Page 39 & 40) -->
<div class="modal fade" id="addVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.ai.vendors.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New API Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Vendor Name <span class="text-danger">*</span></label>
                    <input type="text" name="vendor_name" class="form-control" required placeholder="e.g. OpenAI GPT-4o Enterprise">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Base API Endpoint <span class="text-danger">*</span></label>
                    <input type="url" name="api_base_url" class="form-control" required value="https://api.openai.com/v1">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Model Name <span class="text-danger">*</span></label>
                    <input type="text" name="model_name" class="form-control" required placeholder="gpt-4o">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Secret API Key (Encrypted in DB) <span class="text-danger">*</span></label>
                    <input type="password" name="api_key" class="form-control" required placeholder="sk-...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Admin Email</label>
                    <input type="email" name="email_address" class="form-control" placeholder="vendor@lawuniverse.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Validity Date</label>
                    <input type="date" name="validity_date" class="form-control">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="v_active" value="1" checked>
                        <label class="form-check-label fw-semibold" for="v_active">Set as Active Default Engine</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save API Vendor</button>
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
    document.getElementById('multiRemoveBtn').disabled = (checked === 0);
}
</script>
@endsection
