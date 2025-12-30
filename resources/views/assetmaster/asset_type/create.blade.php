@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">Add Asset Type</h3>
    <div class="card p-4 shadow">

        @if ($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        <form action="{{ route('assetmaster.asset_type.store') }}" method="POST">
            @csrf

            <!-- select Asset -->
              <div class="col-md-12">

                    <label class="form-label">Asset Type</label>

                    <select name="type_name" class="form-select select2-tags asset-dropdown">

                        <option value="">Select or Type City</option>

                        <option>Switch</option>

                        <option>Router</option>

                        <option>SD WAN</option>

                    </select>

                </div>


<div class="p-3">
            <button class="btn btn-primary float-start">Save Asset Type</button>
            <a href="{{ route('assetmaster.asset_type.index') }}" class="btn btn-secondary mt-3 float-end">Cancel</a>

            </div>
        </form>
    </div>
</div>

<script>
// Asset Type Duplicate Validation
document.addEventListener('DOMContentLoaded', function () {
    const assetDropdown = document.querySelector('.asset-dropdown');
    // List of existing asset types (should be passed from backend)
    const existingTypes = ["Switch", "Router", "SD WAN"];

    assetDropdown.addEventListener('change', function () {
        const selected = assetDropdown.value.trim().toLowerCase();
        let isDuplicate = false;
        existingTypes.forEach(function(type) {
            if (type.trim().toLowerCase() === selected) {
                isDuplicate = true;
            }
        });
        if (isDuplicate) {
            assetDropdown.classList.add('is-invalid');
            if (!document.getElementById('duplicateError')) {
                const errorDiv = document.createElement('div');
                errorDiv.id = 'duplicateError';
                errorDiv.className = 'invalid-feedback';
                errorDiv.innerText = 'This asset type already exists.';
                assetDropdown.parentNode.appendChild(errorDiv);
            }
        } else {
            assetDropdown.classList.remove('is-invalid');
            const errorDiv = document.getElementById('duplicateError');
            if (errorDiv) errorDiv.remove();
        }
    });

    // Prevent form submission if duplicate
    const form = assetDropdown.closest('form');
    form.addEventListener('submit', function(e) {
        if (assetDropdown.classList.contains('is-invalid')) {
            e.preventDefault();
            assetDropdown.focus();
        }
    });
});
</script>
@endsection