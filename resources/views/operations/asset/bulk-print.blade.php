<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Asset Print</title>
    <link rel="icon" type="image/png" width="20" height="20" href="{{ asset('images/logo.jpg') }}">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 5px;
            background: #fff;
        }
        /* .asset-card {
            border: 1px dashed #007bff;
            padding: 10px;
            margin-bottom: 18px;
        } */
        /* .asset-card h4 {
            margin: 0 0 12px;
        } */
        /* .asset-properties {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 14px;
        }
        .asset-properties span {
            flex: 1 1 180px;
        } */
        .asset-row {
            margin-bottom: 24px;
            
            
        }
        .asset-row img {
            height: 50px;
            width: 50%;
            max-width: 320px;
            object-fit: contain;
            display: block;
            /* margin: 0 auto 8px; */
        }
        .asset-row p {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.04em;
            margin-left: 70px;
        }
        /* @media print {
            body { padding: 0; }
            .asset-card { page-break-inside: avoid; }
        } */
    </style>
</head>
<body>
    <h2>Print Preview</h2>
    @foreach($assets as $asset)
    <div class="asset-row">
        <img src="/barcode.php?code={{ $asset->asset_id }}" alt="Barcode">
                <p class="">{{ $asset->asset_id }}</p>
    </div>
        
    @endforeach
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
</body>
</html>
