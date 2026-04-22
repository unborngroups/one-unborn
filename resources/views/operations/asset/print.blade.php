<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Asset Label – {{ $asset->asset_id }}</title>
    <link rel="icon" type="image/png" width="20" height="20" href="{{ asset('images/logo.jpg') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @page {
            
            margin: 0;
        }
        body {
            margin: 0;
            text-align: center;
            /* padding: 4mm; */
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #fff;
            /* margin-left: 2mm; */
        }
        
        .company {
            font-size: 10px;
            text-transform: uppercase;
            color: #0f172a;
            /* margin-bottom: 2px; */
            font-weight: 600;
        }
        .label-title {
            font-size: 10px;
            font-weight: 500;
            color: #0f172a;
            /* margin: 6px 0 12px; */
            /* margin-left: 40px;   */
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
<body onload="window.print()">
    <div class="print-wrapper">
        <div class="label">
    <!-- <strong>{{ $asset->asset_id }}</strong><br> -->
    {{ $asset->model_no }}<br>
    <div class="company">  {{$asset->company->company_name ?? ''}} </div>
  
 <img src="/barcode.php?code={{ $asset->asset_id }}" alt="Barcode" style="height:20mm; width:50mm">
        <div class="label-title">{{ $asset->asset_id }}</div>


</div>
        <!--  -->
    </div>
</body>
</html>
