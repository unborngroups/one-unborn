<!DOCTYPE html>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #fff;
            text-align: center;
        }
       
            .company {
            font-size: 10px;
            padding-top: 1mm;
            text-align: center;
            text-transform: uppercase;
            color: #0f172a;
            font-weight: 500;
        }
        .label-title {
            font-size: 10px;
            font-weight: 500;
        }
        @media print {
            body {
                background: #fff;
            }
            .print-wrapper {
                box-shadow: none;
                border: none;
                width: auto;
            }
        }

    </style>
</head>
<body>
    @foreach($assets as $asset)
    <div class="print-wrapper">
        <div class="label">
            <p class="company">{{$asset->company->company_name ?? ''}}</p>
            <div class="barcode-box">
                <img src="/barcode.php?code={{ $asset->asset_id }}" alt="Barcode" style="height:20mm; width:50mm">
            </div>
            <div class="label-title">{{ $asset->asset_id }}</div>
        </div>
    </div>
    @endforeach
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
</body>
</html>
