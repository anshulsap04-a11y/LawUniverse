@extends('layouts.admin', ['title' => 'Manage Coupon - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-ticket-perforated text-danger me-2"></i> Manage Coupon</h4>
        <p class="text-muted small mb-0">Create, manage, and track promotional coupons for subscriptions and purchases (Page 35 & 36).</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
        <i class="bi bi-plus-lg me-1"></i> Create New Coupon
    </button>
</div>

<!-- Filters Panel matching PDF Page 35 & 36 -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.finance.coupons') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search Coupon by code, name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.finance.coupons') }}" class="btn btn-outline-secondary" title="Refresh"><i class="bi bi-arrow-counterclockwise"></i> Refresh</a>
            </div>
        </form>
    </div>
</div>

<form action="{{ route('admin.finance.coupons.bulk_trash') }}" method="POST" id="bulkForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-danger btn-sm fw-semibold" id="multiRemoveBtn" onclick="return confirm('Delete selected coupons?')" disabled>
                <i class="bi bi-trash me-1"></i> Multi Remove
            </button>
            <span class="text-muted small">Showing {{ $coupons->total() }} Promotional Coupons</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Coupon Code & Name</th>
                        <th>Discount Value</th>
                        <th>Usage Limit</th>
                        <th>Validity Period</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions (Page 36)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $c)
                        <tr>
                            <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $c->id }}" class="item-checkbox" onchange="checkSelection()"></td>
                            <td>
                                <strong class="badge bg-warning-subtle text-dark border border-warning fs-6">{{ $c->coupon_code }}</strong>
                                <small class="text-muted d-block">{{ $c->coupon_name }}</small>
                            </td>
                            <td class="fw-bold text-success">
                                @if($c->discount_type === 'percentage')
                                    {{ $c->discount_value }}% OFF
                                @else
                                    &#8377; {{ number_format($c->discount_value, 2) }} Flat
                                @endif
                            </td>
                            <td>{{ $c->usage_limit ?? 'Unlimited' }}</td>
                            <td>
                                <small class="text-muted">
                                    {{ $c->start_date ? \Carbon\Carbon::parse($c->start_date)->format('d M Y') : 'Immediate' }}
                                    &rarr;
                                    {{ $c->expiration_date ? \Carbon\Carbon::parse($c->expiration_date)->format('d M Y') : 'No Expiry' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $c->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <!-- Deactivate / Activate Button (Item 8) -->
                                <button type="button" class="btn btn-sm btn-outline-warning text-dark" onclick="document.getElementById('toggle_{{ $c->id }}').submit()">
                                    {{ $c->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                                <!-- Send / Share (Item 11) -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('Use coupon code {{ $c->coupon_code }} for discount on Law Universe!'); alert('Coupon text copied to clipboard!')" title="Share Coupon">
                                    <i class="bi bi-send"></i> Send
                                </button>
                                <!-- Details (Item 9) -->
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#cModal_{{ $c->id }}">
                                    Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No coupons found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="card-footer bg-white border-0 py-3">{{ $coupons->links() }}</div>
        @endif
    </div>
</form>

@foreach($coupons as $c)
    <form action="{{ route('admin.finance.coupons.toggle', $c->id) }}" method="POST" id="toggle_{{ $c->id }}" class="d-none">@csrf</form>

    <div class="modal fade" id="cModal_{{ $c->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Coupon Details ({{ $c->coupon_code }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">{{ $c->description ?? 'No description.' }}</p>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between"><span>Type:</span> <strong>{{ ucfirst($c->discount_type) }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Value:</span> <strong>{{ $c->discount_value }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Usage Limit:</span> <strong>{{ $c->usage_limit ?? 'Unlimited' }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Status:</span> <strong>{{ ucfirst($c->status) }}</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Add (Page 36) -->
<div class="modal fade" id="addCouponModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.finance.coupons.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create New Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Coupon Code <span class="text-danger">*</span></label>
                    <input type="text" name="coupon_code" class="form-control text-uppercase" required placeholder="e.g. BARPASS25">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Coupon Name <span class="text-danger">*</span></label>
                    <input type="text" name="coupon_name" class="form-control" required placeholder="e.g. 20% Bar Exam Discount">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Discount Type</label>
                    <select name="discount_type" class="form-select">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed (&#8377;)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Discount Value <span class="text-danger">*</span></label>
                    <input type="number" step="0.5" name="discount_value" class="form-control" required value="20">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Usage Limit</label>
                    <input type="number" name="usage_limit" class="form-control" placeholder="Unlimited if blank">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Expiration Date</label>
                    <input type="date" name="expiration_date" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Coupon</button>
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
