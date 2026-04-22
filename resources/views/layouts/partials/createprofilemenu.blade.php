<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('profile.create') }}" class="nav-link text-warning fw-bold">
            <i class="bi bi-person-lines-fill"></i> Complete Your Profile
        </a>
    </li>
    <li class="nav-item mt-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </li>
</ul>
