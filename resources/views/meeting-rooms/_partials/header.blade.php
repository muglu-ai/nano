
     <div class="header">
                <div class="header-content d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="event-title">
                            
                            {{ config('app.name', 'SEMICON India') }}
                             </h1>
                <p class="event-subtitle">Meeting Room Booking</p>
                    </div>

                    <!-- User Info Dropdown -->
                    <div class="user-info dropdown" style="z-index: 9999;">       <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle fa-lg"></i>
                            <span class="fw-medium ms-2">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down ms-1"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            {{-- <li>
                                <a class="dropdown-item" href="{{ url('/') }}">
                                    <i class="fas fa-home me-2"></i>Home
                                </a>
                            </li> --}}
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>       </div>
            </div>
      