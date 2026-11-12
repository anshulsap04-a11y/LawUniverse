@extends('layouts.admin', ['title' => 'Transactions - Law Universe'])

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-receipt text-danger me-2"></i> Financial Transactions & Invoices</h4>
    <p class="text-muted small mb-0">Audit trail of all customer payments, invoice IDs, payment methods, and statuses.</p>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Transaction #</th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Purpose</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr>
                        <td class="ps-3 fw-bold text-dark">{{ $t->transaction_number }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $t->invoice_number }}</span></td>
                        <td>
                            <div>{{ $t->user->full_name ?? 'User' }}</div>
                            <small class="text-muted">{{ $t->user->email ?? '' }}</small>
                        </td>
                        <td><span class="badge bg-primary-subtle text-primary">{{ ucfirst(str_replace('_', ' ', $t->payment_purpose)) }}</span></td>
                        <td class="fw-bold text-success">&#8377; {{ number_format($t->amount, 2) }}</td>
                        <td>{{ strtoupper($t->payment_method) }}</td>
                        <td>
                            <span class="badge bg-{{ $t->status === 'successful' ? 'success' : 'warning' }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                        <td>{{ $t->created_at->format('d M, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No transactions recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
