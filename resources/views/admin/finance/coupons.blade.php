@extends('layouts.admin', ['title' => 'Coupons - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-ticket-perforated text-danger me-2"></i> Promotional Discount Coupons</h4>
        <p class="text-muted small mb-0">Create percentage or flat vouchers, usage limits, and validity windows.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
        <i class="bi bi-plus-lg me-1"></i> Create Coupon
    </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Coupon Code</th>
                    <th>Discount</th>
                    <th>Usage / Limit</th>
                    <th>Validity Period</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $c)
                    <tr>
                        <td class="ps-3"><strong class="badge bg-warning-subtle text-dark border border-warning fs-6">{{ $c->code }}</strong></td>
                        <td class="fw-bold text-success">
                            @if($c->discount_type === 'percentage')
                                {{ $c->discount_value }}% OFF
                            @else
                                &#8377; {{ number_format($c->discount_value, 2) }} Flat
                            @endif
                        </td>
                        <td>{{ $c->used_count }} / {{ $c->max_uses ?? 'Unlimited' }} uses</td>
                        <td>
                            <small class="text-muted">
                                {{ $c->start_date ? \Carbon\Carbon::parse($c->start_date)->format('d M Y') : 'Immediate' }}
                                &rarr;
                                {{ $c->end_date ? \Carbon\Carbon::parse($c->end_date)->format('d M Y') : 'No Expiry' }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $c->is_active ? 'success' : 'secondary' }}">
                                {{ $c->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No coupons created.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($coupons->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $coupons->links() }}</div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.finance.coupons.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Coupon Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control text-uppercase" required placeholder="e.g. BARPASS25">
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
                    <label class="form-label fw-semibold">Max Uses Limit</label>
                    <input type="number" name="max_uses" class="form-control" placeholder="Leave blank for unlimited">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="c_active" value="1" checked>
                        <label class="form-check-label fw-semibold" for="c_active">Active & Redeemable</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Coupon</button>
            </div>
        </form>
    </div>
</div>
@endsection
