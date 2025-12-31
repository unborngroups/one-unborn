@extends('layouts.app')



@section('content')

<div class="container-fluid">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header bg-info text-white">

                    <h4 class="mb-0">

                        <i class="bi bi-eye"></i> Purchase Order Details - {{ $purchaseOrder->po_number }}

                    </h4>

                </div>

                <div class="card-body">

                    {{-- PO Header Information --}}

                    <div class="row mb-4">

                        <div class="col-md-6">

                            <table class="table table-borderless">

                                <tr>

                                    <th width="40%">PO Number:</th>

                                    <td><strong class="text-primary">{{ $purchaseOrder->po_number }}</strong></td>

                                </tr>

                                <tr>

                                    <th>PO Date:</th>

                                    <td>{{ $purchaseOrder->po_date->format('Y-m-d') }}</td>

                                </tr>

                                <tr>

                                    <th>Feasibility ID:</th>

                                    <td>

                                        <span class="badge bg-info">{{ $purchaseOrder->feasibility->feasibility_request_id ?? 'N/A' }}</span>

                                    </td>

                                </tr>

                                <tr>

                                    <th>Client:</th>

                                    <td>{{ $purchaseOrder->feasibility->client->client_name ?? 'N/A' }}</td>

                                </tr>

                            </table>

                        </div>

                        <div class="col-md-6">

                            <table class="table table-borderless">

                                <tr>

                                    <th>Status:</th>

                                    <td><span class="badge bg-success">Active</span></td>

                                </tr>

                                <tr>

                                    <th>Contract Period:</th>

                                    <td>{{ $purchaseOrder->contract_period }} Months</td>

                                </tr>

                                <tr>

                                    <th>No. of Links:</th>

                                    <td>

                                        <span class="badge bg-primary">{{ $purchaseOrder->no_of_links }} Links</span>

                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>



                    {{-- Enhanced Pricing Details (Simplified Link Display) --}}

                    <div class="card mb-4">

                        <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">

                            <h5 class="mb-0 text-white">

                                <i class="bi bi-currency-rupee"></i> Pricing Details (Per Link Breakdown)

                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                @for($i = 1; $i <= $purchaseOrder->no_of_links; $i++)

                                <div class="col-md-6 mb-3">

                                    <div class="card border-primary">

                                        <div class="card-header bg-primary text-white">

                                            <h6 class="mb-0">

                                                <i class="bi bi-link-45deg"></i> Link {{ $i }} Pricing

                                            </h6>

                                        </div>

                                        <div class="card-body">

                                            <table class="table table-sm mb-0">

                                                @php

                                                    // Get individual link amounts, fall back to per_link if not available

                                                    $arcAmount = $purchaseOrder->{"arc_link_{$i}"} ?? $purchaseOrder->arc_per_link;

                                                    $otcAmount = $purchaseOrder->{"otc_link_{$i}"} ?? $purchaseOrder->otc_per_link;

                                                    $staticIpAmount = $purchaseOrder->{"static_ip_link_{$i}"} ?? $purchaseOrder->static_ip_cost_per_link;

                                                @endphp

                                                <tr>

                                                    <td><strong>ARC:</strong></td>

                                                    <td class="text-end">₹{{ number_format($arcAmount, 2) }}</td>

                                                </tr>

                                                <tr>

                                                    <td><strong>OTC:</strong></td>

                                                    <td class="text-end">₹{{ number_format($otcAmount, 2) }}</td>

                                                </tr>

                                                <tr>

                                                    <td><strong>Static IP:</strong></td>

                                                    <td class="text-end">₹{{ number_format($staticIpAmount, 2) }}</td>

                                                    </tr>

                                                    <tr class="table-light">

                                                        <td><strong>Link {{ $i }} Total:</strong></td>

                                                        <td class="text-end"><strong>₹{{ number_format($arcAmount + $otcAmount + $staticIpAmount, 2) }}</strong></td>

                                                    </tr>

                                                </table>

                                            </div>

                                        </div>

                                    </div>

                                @endfor

                            </div>



                            {{-- Summary Table --}}

                            <div class="row mt-4">

                                <div class="col-12">

                                    <h6 class="text-primary mb-3">

                                        <i class="bi bi-calculator"></i> Pricing Summary

                                    </h6>

                                    <table class="table table-bordered">

                                        <thead class="table-dark">

                                            <tr>

                                                <th>Component</th>

                                                <th class="text-center">Per Link (₹)</th>

                                                <th class="text-center">No. of Links</th>

                                                <th class="text-end">Total Amount (₹)</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            <tr>

                                                <td><strong>ARC</strong> (Annual Rental Charges)</td>

                                                <td class="text-center">{{ number_format($purchaseOrder->arc_per_link, 2) }}</td>

                                                <td class="text-center">{{ $purchaseOrder->no_of_links }}</td>

                                                <td class="text-end">{{ number_format($purchaseOrder->arc_per_link * $purchaseOrder->no_of_links, 2) }}</td>

                                            </tr>

                                            <tr>

                                                <td><strong>OTC</strong> (One Time Charges)</td>

                                                <td class="text-center">{{ number_format($purchaseOrder->otc_per_link, 2) }}</td>

                                                <td class="text-center">{{ $purchaseOrder->no_of_links }}</td>

                                                <td class="text-end">{{ number_format($purchaseOrder->otc_per_link * $purchaseOrder->no_of_links, 2) }}</td>

                                            </tr>

                                            <tr>

                                                <td><strong>Static IP Cost</strong></td>

                                                <td class="text-center">{{ number_format($purchaseOrder->static_ip_cost_per_link, 2) }}</td>

                                                <td class="text-center">{{ $purchaseOrder->no_of_links }}</td>

                                                <td class="text-end">{{ number_format($purchaseOrder->static_ip_cost_per_link * $purchaseOrder->no_of_links, 2) }}</td>

                                            </tr>

                                        </tbody>

                                        <tfoot class="table-success">

                                            <tr>

                                                <th colspan="3"><i class="bi bi-cash-stack"></i> Grand Total</th>

                                                <th class="text-end">₹{{ number_format(($purchaseOrder->arc_per_link + $purchaseOrder->otc_per_link + $purchaseOrder->static_ip_cost_per_link) * $purchaseOrder->no_of_links, 2) }}</th>

                                            </tr>

                                        </tfoot>

                                    </table>

                                </div>

                            </div>

                        </div>

                        <!--  -->
                        


                    </div>

<tr>
    <th>Import File:</th>
    <td>
        @if($purchaseOrder->import_file)
            <a href="{{ asset($purchaseOrder->import_file) }}"
               target="_blank"
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-earmark-arrow-down"></i> View File
            </a>
        @else
            <span class="text-muted">No File Uploaded</span>
        @endif
    </td>
</tr>

                    {{-- Action Buttons --}}

                    <div class="row">

                        <div class="col-12 d-flex justify-content-between align-items-center">

                            <div>

                                <small class="text-muted">

                                    <i class="bi bi-info-circle"></i> 

                                    This Purchase Order supports multi-vendor pricing validation

                                </small>

                            </div>

                            <div>

                                <a href="{{ route('sm.purchaseorder.index') }}" class="btn btn-secondary me-2">

                                    <i class="bi bi-arrow-left"></i> Back to List

                                </a>

                                

                            </div>

                        </div>

                    </div>



                </div>

            </div>

        </div>

    </div>

</div>

@endsection

