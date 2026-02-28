@extends('layouts.app')

@section('content')

<div class="container py-4">




    <div class="card shadow border-0 p-4">


    <h3 class="mb-3 text-primary">View Item</h3>

        <table class="table table-bordered">

            <tr>

                <th>Name</th>

                <td>{{ $items->item_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>Description</th>

                <td>{{ $items->item_description ?? '-' }}</td>

            </tr>

            <tr>

                <th>Rate</th>

                <td>{{ $items->item_rate ?? '-' }}</td>

            </tr>

            <tr>

                <th>HSN / SAC</th>

                <td>{{ $items->hsn_sac_code ?? '-' }}</td>

            </tr>

            <tr>

                <th>Unit</th>

                <td>{{ $items->usage_unit ?? '-' }}</td>

            </tr>

            <tr>

                <th>Status</th>

                <td>

                    <span class="badge {{ $items->status === 'Active' ? 'bg-success' : 'bg-danger' }}">

                        {{ $items->status }}

                    </span>

                </td>

            </tr>

        </table>



        <div class="text-end">

            <a href="{{ route('finance.items.index') }}" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

@endsection

