@extends('layouts.admin', ['title' => 'Dynamic RBAC & Module Permissions - Law Universe'])

@section('content')
<div class="content-card p-4">

    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="bi bi-shield-lock-fill text-primary me-2"></i> Dynamic RBAC & Module Permission Matrix
            </h4>
            <span class="text-muted small">Configure module and submodule level granular permissions across system roles.</span>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#newRoleModal">
                <i class="bi bi-plus-circle me-1"></i> Add Custom Role
            </button>
        </div>
    </div>

    <!-- Role Selection Tabs -->
    <div class="card bg-light-subtle p-3 mb-4 border">
        <label class="small fw-bold text-secondary mb-2 d-block">Select Role to Configure Permissions:</label>
        <div class="d-flex flex-wrap gap-2">
            @foreach($roles as $r)
                <a href="{{ route('admin.rbac.index', ['role_id' => $r->id]) }}" class="btn btn-sm {{ $selectedRole && $selectedRole->id === $r->id ? 'btn-dark active' : 'btn-outline-secondary' }} px-3 py-2 fw-semibold">
                    <i class="bi bi-person-badge me-1"></i> {{ $r->display_name }}
                    @if($r->is_system)
                        <span class="badge bg-secondary-subtle text-dark ms-1" style="font-size: 9px;">System</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    @if($selectedRole)
        <form action="{{ route('admin.rbac.matrix.update') }}" method="POST">
            @csrf
            <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark">
                    Permissions for: <span class="text-primary">{{ $selectedRole->display_name }}</span>
                </h5>
                <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2 shadow-sm">
                    <i class="bi bi-floppy-fill me-1"></i> Save Dynamic Permissions
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark small">
                        <tr>
                            <th width="35%">Module / Sub-Module Name</th>
                            <th width="15%" class="text-center">Can View</th>
                            <th width="15%" class="text-center">Can Create</th>
                            <th width="15%" class="text-center">Can Edit</th>
                            <th width="15%" class="text-center">Can Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $mod)
                            @php
                                $modPerm = $permissionsMatrix[$selectedRole->id][$mod->id][0] ?? null;
                            @endphp
                            <!-- Parent Module Row -->
                            <tr class="table-secondary fw-bold">
                                <td>
                                    <i class="bi {{ $mod->icon ?? 'bi-box' }} text-primary me-2"></i>
                                    {{ $mod->name }}
                                </td>
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox" name="permissions[{{ $mod->id }}][0][can_view]" value="1" {{ !empty($modPerm['can_view']) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox" name="permissions[{{ $mod->id }}][0][can_create]" value="1" {{ !empty($modPerm['can_create']) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox" name="permissions[{{ $mod->id }}][0][can_edit]" value="1" {{ !empty($modPerm['can_edit']) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox" name="permissions[{{ $mod->id }}][0][can_delete]" value="1" {{ !empty($modPerm['can_delete']) ? 'checked' : '' }}>
                                </td>
                            </tr>

                            <!-- Submodules Rows -->
                            @foreach($mod->submodules as $sub)
                                @php
                                    $subPerm = $permissionsMatrix[$selectedRole->id][$mod->id][$sub->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <i class="bi bi-arrow-return-right text-muted me-2"></i>
                                        <span class="small fw-semibold text-secondary">{{ $sub->name }}</span>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" name="permissions[{{ $mod->id }}][{{ $sub->id }}][can_view]" value="1" {{ !empty($subPerm['can_view']) ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" name="permissions[{{ $mod->id }}][{{ $sub->id }}][can_create]" value="1" {{ !empty($subPerm['can_create']) ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" name="permissions[{{ $mod->id }}][{{ $sub->id }}][can_edit]" value="1" {{ !empty($subPerm['can_edit']) ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" name="permissions[{{ $mod->id }}][{{ $sub->id }}][can_delete]" value="1" {{ !empty($subPerm['can_delete']) ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2 shadow-sm">
                    <i class="bi bi-floppy-fill me-1"></i> Save Dynamic Permissions
                </button>
            </div>
        </form>
    @endif

</div>

<!-- New Role Modal -->
<div class="modal fade" id="newRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.rbac.roles.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="display_name" class="form-label small fw-semibold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="display_name" name="display_name" required placeholder="e.g. Legal Researcher, Law Clerk">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label small fw-semibold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Explain the responsibilities and scope of this role"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Create Role</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

