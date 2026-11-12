@extends('layouts.admin', ['title' => 'Finance Statistics - Law Universe'])

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-bar-chart text-danger me-2"></i> Financial & Subscription Analytics</h4>
    <p class="text-muted small mb-0">Overview of subscription cashflows, package sales, invoice reconciliations, and coupons.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Total Revenue</h6>
                    <h3 class="fw-bold mb-0">&#8377; {{ number_format($totalRevenue, 2) }}</h3>
                </div>
                <i class="bi bi-currency-rupee fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Successful Payments</h6>
                    <h3 class="fw-bold mb-0">{{ $successfulTxns }}</h3>
                </div>
                <i class="bi bi-check-circle-fill fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Active Subscriptions</h6>
                    <h3 class="fw-bold mb-0">{{ $activeSubs }}</h3>
                </div>
                <i class="bi bi-person-check-fill fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-warning text-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Active Coupons</h6>
                    <h3 class="fw-bold mb-0">{{ $totalCoupons }}</h3>
                </div>
                <i class="bi bi-ticket-perforated-fill fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Recent Successful Invoices & Transactions</h6>
        <a href="{{ route('admin.finance.transactions') }}" class="btn btn-sm btn-outline-primary">View All Transactions</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Txn / Invoice #</th>
                    <th>User</th>
                    <th>Package / Item</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Paid At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $t)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $t->transaction_number }}</div>
                            <small class="text-muted">{{ $t->invoice_number }}</small>
                        </td>
                        <td>
                            <div>{{ $t->user->full_name ?? 'Customer' }}</div>
                            <small class="text-muted">{{ $t->user->email ?? '' }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $t->package->name ?? 'General Resource' }}</span></td>
                        <td class="fw-bold text-success">&#8377; {{ number_format($t->amount, 2) }}</td>
                        <td><span class="badge bg-secondary-subtle text-secondary">{{ strtoupper($t->payment_method) }}</span></td>
                        <td>{{ $t->paid_at ? \Carbon\Carbon::parse($t->paid_at)->format('d M, Y H:i') : 'Pending' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No transactions recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
