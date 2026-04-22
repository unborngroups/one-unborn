<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Portal - @yield('title')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    {{-- NAVBAR --}}
    <nav class="navbar navbar-dark bg-primary px-3">
        <a class="navbar-brand fw-bold text-white">Client Portal</a>

        <div class="d-flex align-items-center">
            <span class="text-white me-3">{{ Auth::guard('client')->user()->portal_username }}</span>
            <a href="{{ route('client.logout') }}" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </nav>

    <div class="d-flex">
        {{-- SIDEBAR --}}
        <aside class="bg-dark text-white p-3" style="width: 230px; min-height: 100vh;">
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a href="{{ route('client.dashboard') }}" class="nav-link text-white">Dashboard</a></li>
                <li class="nav-item mb-2"><a href="{{ route('client.links') }}" class="nav-link text-white">My Links</a></li>
                <li class="nav-item mb-2"><a href="{{ route('client.notifications.settings') }}" class="nav-link text-white">Notifications</a></li>
            </ul>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="p-4 w-100">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
