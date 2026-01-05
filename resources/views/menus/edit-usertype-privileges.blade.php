@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold text-primary mb-4">
        Manage User Type Privileges — <span class="text-dark">{{ $userType->name }}</span>
    </h3>


    <div class="card shadow-lg border-0 rounded-4 p-4">
        <form id="userTypePrivilegeForm" action="{{ route('menus.updateUserTypePrivileges', $userType->id) }}" method="POST">
            @csrf

            {{-- ✅ Global Grant All --}}
            <div class="form-check mb-4">
                <input type="checkbox" class="form-check-input" id="grantAllGlobal">
                <label class="form-check-label fw-semibold" for="grantAllGlobal">Grant All Permissions for User Type</label>
            </div>
            {{-- ✅ Privilege Table --}}
            <div class="table-responsive privilege-table">
                <table class="table table-bordered align-middle text-center shadow-sm mb-0">
                    <thead class="table-primary table-custom">
                        <tr>
                            <th class="select-col">Select</th>
                            <th class="module-col">Module</th>
                            <th class="section-col">Section</th>
                            <th class="sub-section-col">Sub-Section</th>

                            <th class="menu-col">Menu</th>
                            <th>View</th>
                            <th>Add</th>
                            <th>Edit</th>
                            <th>Delete</th>
                            <th>All</th>
                            <!-- <th>Others</th> -->
                        </tr>
                    </thead>
                    <tbody>

                    @foreach ($groupedMenus as $moduleName => $moduleMenus)
                        @php
                              $moduleSlug = \Illuminate\Support\Str::slug($moduleName ?: 'module-'.$loop->index, '-');
                        @endphp

                        @foreach ($moduleMenus as $menu)
                            @php
                                $priv = $userTypePrivileges[$menu->id] ?? null;
                            @endphp
                            <tr data-module="{{ $moduleSlug }}">
                                <td class="text-center select-cell">
                                    @if ($loop->first)
                                        <input type="checkbox" class="module-checkbox" data-module="{{ $moduleSlug }}">
                                    @endif
                                </td>
                                <td class="text-start fw-semibold module-cell">
                                    @if ($loop->first)
                                        {{ ucfirst($moduleName) }}
                                    @endif
                                </td>
                                <!-- <td class="text-start section-cell">
                                    {{ ucfirst($moduleName) }}
                                </td> -->
                                <td class="text-start menu-cell ps-3">
                                    {{ $menu->name }}
                                </td>

                                    <td>
                                        <small>
                                        {{ ucfirst($menu->sub_section) }}
                                        </small>
                                    </td>


                                <td>
                                    <input type="checkbox" name="privileges[{{ $menu->id }}][can_menu]" value="1"
                                           class="perm-checkbox"
                                           {{ $priv && $priv->can_menu ? 'checked' : '' }}>
                                </td>

                                <td>
                                    <input type="checkbox" name="privileges[{{ $menu->id }}][can_view]" value="1"
                                           class="perm-checkbox"
                                           {{ $priv && $priv->can_view ? 'checked' : '' }}>
                                </td>

                                <td>
                                    <input type="checkbox" name="privileges[{{ $menu->id }}][can_add]" value="1"
                                           class="perm-checkbox"
                                           {{ $priv && $priv->can_add ? 'checked' : '' }}>
                                </td>

                                <td>
                                    <input type="checkbox" name="privileges[{{ $menu->id }}][can_edit]" value="1"
                                           class="perm-checkbox"
                                           {{ $priv && $priv->can_edit ? 'checked' : '' }}>
                                </td>

                                <td>
                                    <input type="checkbox" name="privileges[{{ $menu->id }}][can_delete]" value="1"
                                           class="perm-checkbox"
                                           {{ $priv && $priv->can_delete ? 'checked' : '' }}>
                                </td>

                                <td>
                                        <input type="checkbox" class="grant-row-all form-check-input"
                                               title="Grant all permissions for this menu">
                                    </td>
                            </tr>
                        @endforeach
                    @endforeach

                    </tbody>
                </table>
            </div>

            {{-- ✅ Buttons --}}
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success px-5 py-2 rounded-3 shadow-sm me-2">
                    <i class="bi bi-check-circle me-1"></i> Save User Type Privileges
                </button>
                <a href="{{ route('usertypetable.index') }}" class="btn btn-secondary px-5 py-2 rounded-3">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ✅ JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const globalGrant = document.getElementById('grantAllGlobal');

    globalGrant.addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('tbody input[type="checkbox"]:not(:disabled)').forEach(cb => cb.checked = checked);
    });

    document.querySelectorAll('.grant-row-all').forEach(rowCb => {
        rowCb.addEventListener('change', function () {
            const row = this.closest('tr');
            const checked = this.checked;
            row.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = checked);
        });
    });

    document.querySelectorAll('.module-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const moduleKey = this.dataset.module;
            const checked = this.checked;
            document.querySelectorAll(`tr[data-module="${moduleKey}"] .perm-checkbox`).forEach(button => button.checked = checked);
        });
    });
});
</script>

{{-- ✅ Styling --}}
<style>
.privilege-table {
    border: 1px solid #dfe4eb;
    border-radius: 10px;
    background: #fff;
    padding: 0.35rem;
}
.privilege-table .table {
    margin-bottom: 0;
}
.table-custom th {
    background: linear-gradient(135deg, #0d6efd, #0a52c4) !important;
    color: #fff;
    border-color: #0a58ca;
    font-size: 0.9rem;
    letter-spacing: 0.03rem;
    text-transform: uppercase;
}
.table tbody tr:nth-of-type(odd) {
    background: #f6fafd;
}
.table tbody tr:nth-of-type(even) {
    background: #ffffff;
}
.table td,
.table th {
    border-color: #cce0ff;
    vertical-align: middle;
    padding: 0.7rem 0.55rem;
}
.select-col {
    width: 5%;
}
.module-col {
    width: 15%;
}
.section-col {
    width: 15%;
}
.menu-col {
    width: 20%;
    text-align: left;
}
.module-cell,
.section-cell,
.menu-cell {
    font-size: 0.95rem;
    color: #1c1f26;
}
.text-center input[type="checkbox"] {
    cursor: pointer;
    width: 1.1rem;
    height: 1.1rem;
}
.text-center input[type="checkbox"]:focus-visible {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.45);
}
.table-responsive {
    border-radius: 12px;
    background: #fdfdfd;
}
.perm-checkbox {
    accent-color: #0d6efd;
}
.btn-success {
    background-color: #198754;
    border: none;
}
.btn-success:hover {
    background-color: #157347;
}
.btn-secondary {
    background-color: #6c757d;
    border: none;
}
.btn-secondary:hover {
    background-color: #5c636a;
}
.form-check-label {
    font-weight: 600;
}
@media (max-width: 992px) {
    .select-col,
    .module-col,
    .section-col,
    .menu-col {
        width: auto;
    }
}
</style>

@endsection