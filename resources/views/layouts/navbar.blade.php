<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-light shadow-sm">
  <div class="container-fluid px-2" style="min-height:60px;">
    <!-- Sidebar toggle + Logo -->
    <div class="d-flex align-items-center flex-shrink-0">
      <button class="btn btn-dark me-2" id="sidebarToggle" style="border: none; font-size: 1.4rem; padding: 6px 10px; min-width: 40px; min-height: 40px;">
        <i id="sidebarToggleIcon" class="bi bi-chevron-left" style="color: white;"></i>
      </button>
      <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
        <img src="{{ asset('images/logo1.png') }}" alt="One-Unborn" class="navbar-logo"
          style="height:40px; max-width:120px; object-fit:contain; border-radius:4px; margin-right:6px;">
      </a>
    </div>
    <!-- Centered Time (on mobile, moves to right on desktop) -->
    @auth
    @if(isset($clockDisplay) || isset($onlineSince))
      <span id="onlineStatusTicker" class="mx-2 fw-semibold text-white text-nowrap" style="font-size: 15px;">
        ⏱
        @if(isset($clockDisplay))
          <span id="onlineSinceValue">{{ $clockDisplay }}</span>
        @else
          <span id="onlineSinceValue">Online since {{ $onlineSince }}</span>
        @endif
        @if(!empty($onlineDurationLabel))
          (<span id="onlineDurationLabel">{{ $onlineDurationLabel }}</span>)
        @else
          (<span id="onlineDurationLabel">0s</span>)
        @endif
      </span>
    @endif
    <!-- Profile Dropdown -->
    <div class="dropdown ms-2 flex-shrink-0">
      <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button"
        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="{{ asset(optional(Auth::user()->profile)->profile_photo 
          ? Auth::user()->profile->profile_photo 
          : 'images/default.png') }}"
          alt="Profile" class="rounded-circle" width="35" height="35"
          style="object-fit: cover; border: 2px solid #e7eaeeff;">
        <span class="ms-2 fw-semibold text-white d-none d-md-inline">{{ Auth::user()->name }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="{{ route('profile.view') }}"><i class="bi bi-person me-2"></i> View Profile</a></li>
        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-pencil-square me-2"></i> Edit Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
          </form>
        </li>
      </ul>
    </div>
    @endauth
  </div>
</nav>
<!-- Responsive Navbar/Profile/Time CSS for Mobile -->
<style>
@media (max-width: 767px) {
  .navbar .container-fluid {
    flex-wrap: nowrap !important;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }
  .navbar .navbar-brand {
    margin-right: 0 !important;
  }
  #onlineStatusTicker {
    order: 3;
    margin-left: auto !important;
    margin-right: 0 !important;
    display: flex;
    align-items: center;
    font-size: 13px !important;
    background: #00113a;
    border-radius: 6px;
    padding: 2px 8px;
  }
  .navbar .dropdown {
    order: 4;
    margin-left: 8px !important;
  }
  .navbar .d-flex.align-items-center {
    order: 1;
  }
}
@media (min-width: 768px) {
  #onlineStatusTicker {
    margin-left: 16px !important;
    font-size: 15px !important;
    background: transparent;
    padding: 0;
  }
}
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ticker = document.getElementById('onlineStatusTicker');
        if (!ticker || !ticker.dataset.loginTime) {
            return;
        }
        const durationSpan = document.getElementById('onlineDurationLabel');
        const loginTime = new Date(ticker.dataset.loginTime);
        const formatDuration = (milliseconds) => {
            const totalSeconds = Math.floor(milliseconds / 1000);
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            const parts = [];
            if (hours) {
                parts.push(hours + 'h');
            }
            if (minutes) {
                parts.push(minutes + 'm');
            }
            parts.push(seconds + 's');
            return parts.join(' ');
        };
        const updateDuration = () => {
            const elapsed = Math.max(Date.now() - loginTime.getTime(), 0);
            if (durationSpan) {
                durationSpan.textContent = formatDuration(elapsed);
            }
        };
        updateDuration();
        setInterval(updateDuration, 1000);
    });
</script>