@extends('layouts.app')

@section('content')
<div class="container">

    <h4 class="mb-3">Add Asset</h4>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ERROR MESSAGE --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

     @php
            $importRow = session('imported_row', []);
        @endphp
     
    <!-- <h5 class="mb-3 ">Import Assets</h5> -->
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <button class="btn btn-info mb-2" type="button" onclick="toggleImportAsset()">Import Assets via Excel</button>
                <div id="importAssetBox" style="display:none;">
                    <div class="card border-info">
                        <div class="card-body">
                            <p class="mb-3 small text-muted">Download the sample format, populate it with Asset data, and then upload it via Import Excel.</p>
                            <form action="{{ route('operations.asset.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group">
                                    <input type="file" name="file" class="form-control" required  accept=".xlsx, .xls,.csv,.xlsm,.ods">
                                    <a href="{{ asset('images/assets/assets (10).xlsx') }}" target="_blank" class="btn btn-outline-secondary" title="Download asset sample">Download Format</a>
                                    <button type="submit" class="btn btn-primary">Import Excel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                    function toggleImportAsset() {
                        var box = document.getElementById('importAssetBox');
                        box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
                    }
                </script>
            </div>
        </div>


    {{-- SHOW IMPORT ERRORS --}}
    @if(session('import_errors'))
        <div class="alert alert-warning mt-2">
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

</div>

    
    {{-- VALIDATION ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- MAIN FORM --}}
    <form action="{{ route('operations.asset.store') }}" method="POST">
        @csrf

        @include('operations.asset.form')

        <button type="submit" class="btn btn-primary mt-1 float-start">Save</button>

        <a href="{{ route('operations.asset.index') }}" class="btn btn-secondary mt-1 float-end">
            <-- Back
        </a>
    </form>

</div>
@endsection

@section('scripts')
<script>
    // Open the import box only on first click, do not toggle closed
    document.getElementById('importExcelBtn')?.addEventListener('click', function () {
        var importCard = document.getElementById('importCard');
        if (importCard && !importCard.classList.contains('show')) {
            var collapse = bootstrap.Collapse.getOrCreateInstance(importCard);
            collapse.show();
        }
    });
</script>
@endsection