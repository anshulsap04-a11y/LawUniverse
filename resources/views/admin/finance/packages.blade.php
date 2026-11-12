@extends('layouts.admin', ['title' => 'Subscription Packages - Law Universe'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-box-seam text-danger me-2"></i> Subscription Packages</h4>
        <p class="text-muted small mb-0">Configure pricing plans, billing durations, and bundle platform capabilities.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPackageModal">
        <i class="bi bi-plus-lg me-1"></i> Create Subscription Package
    </button>
</div>

<div class="row g-4">
    @forelse($packages as $pkg)
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">{{ $pkg->name }}</h5>
                        <span class="badge bg-{{ $pkg->is_active ? 'success' : 'secondary' }}">
                            {{ $pkg->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="fs-2 fw-bold text-primary">&#8377; {{ number_format($pkg->price, 2) }}</span>
                        <span class="text-muted">/ {{ $pkg->duration_in_days }} days</span>
                    </div>
                    <p class="text-muted small mb-3">{{ $pkg->description }}</p>
                    <h6 class="fw-bold text-dark small text-uppercase mb-2">Included Features:</h6>
                    <ul class="list-unstyled small mb-0">
                        @forelse($pkg->features as $f)
                            <li class="mb-2 text-success">
                                <i class="bi bi-check-circle-fill me-1"></i> {{ $f->name }}
                            </li>
                        @empty
                            <li class="text-muted fst-italic">Standard platform access</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card border-0 shadow-sm p-4 text-center text-muted">No subscription packages created.</div></div>
    @endforelse
</div>

<!-- Modal -->
<div class="modal fade" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.finance.packages.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create Subscription Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Package Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Advocate Pro Plan">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Price (&#8377;) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="price" class="form-control" required value="999.00">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Duration (Days) <span class="text-danger">*</span></label>
                    <input type="number" name="duration_in_days" class="form-control" required value="365">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Bundle Features</label>
                    <div class="row g-2">
                        @foreach($features as $feat)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="selected_features[]" value="{{ $feat->id }}" id="f_{{ $feat->id }}">
                                    <label class="form-check-label small" for="f_{{ $feat->id }}">
                                        <strong>{{ $feat->name }}</strong> ({{ $feat->code }})
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="pkg_active" value="1" checked>
                        <label class="form-check-label fw-semibold" for="pkg_active">Activate Package for Public Purchase</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Package</button>
            </div>
        </form>
    </div>
</div>
@endsection
