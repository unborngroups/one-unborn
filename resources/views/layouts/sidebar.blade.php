<!-- Sidebar -->

<aside id="sidebar">

    <!-- <h4 class="text-center mb-4">Menu</h4> -->

    



    @if(Auth::check())

        @php

            $user = Auth::user();

            $role = strtolower(optional($user->userType)->name);

            $menus = \App\Http\Controllers\Controller::getUserMenus();

        @endphp



        {{-- � Case 1: Profile Created → Show Full Menu (ALL user types) --}}

        @if($user->profile_created)

            @include('layouts.partials.fullmenu', ['menus' => $menus])



        {{-- � Case 2: Profile NOT Created → Show Only Profile Creation Menu (ALL user types) --}}

        @else

            @include('layouts.partials.createprofilemenu')

        @endif

    @endif

</aside>

