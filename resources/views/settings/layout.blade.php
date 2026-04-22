@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <h3 class="mb-4 fw-bold text-primary">Settings</h3>



    <div class="row">

        <!-- Left Side Tabs -->

        <div class="col-md-3">

            <div class="list-group shadow-sm rounded-3">

                <a href="{{ route('company.settings') }}" 

                   class="list-group-item list-group-item-action {{ request()->is('company-settings') ? 'active' : '' }}">

                    <i class="bi bi-building"></i> Company Settings

                </a>

                <a href="{{ route('tax.invoice') }}" 

                   class="list-group-item list-group-item-action {{ request()->is('tax-invoice-settings') ? 'active' : '' }}">

                    <i class="bi bi-receipt"></i> Tax & Invoice Settings

                </a>

                <a href="{{ route('system.settings') }}" 

                   class="list-group-item list-group-item-action {{ request()->is('system-settings') ? 'active' : '' }}">

                    <i class="bi bi-sliders"></i> System Settings

                </a>

            </div>

        </div>



        <!-- Right Side Content -->

        <div class="col-md-9">

            <div class="card border-0 shadow-lg p-4 rounded-4 bg-white">

                @yield('settings-content')

            </div>

        </div>

    </div>

</div>

@endsection

