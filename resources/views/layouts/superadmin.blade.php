<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SERVQUAL Super Admin | POLMED')</title>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* ===== SIDEBAR STYLE ===== */
        .sidebar-gradient {
            background: linear-gradient(135deg, #4c1d95 0%, #2e1065 100%);
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #5b21b6;
            border-radius: 10px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #2e1065;
            border-radius: 10px;
        }

        .nav-active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 4px solid #c084fc;
        }

        .hover-bg-white-10:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .hover-bg-light:hover {
            background-color: #f8f9fa;
        }

        /* ===== CARD HOVER ===== */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        /* ===== MOBILE BOTTOM NAVBAR ===== */
        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 8px 0;
        }

        .mobile-nav .nav-item {
            flex: 1;
            text-align: center;
            color: #6c757d;
            transition: all 0.2s;
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .mobile-nav .nav-item.active {
            color: #7c3aed;
        }

        .mobile-nav .nav-item i {
            font-size: 1.5rem;
        }

        .mobile-nav .nav-item span {
            font-size: 0.7rem;
            display: block;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .mobile-nav {
                display: flex;
            }

            .main-content {
                margin-bottom: 70px;
            }

            #sidebar {
                position: fixed;
                left: -300px;
                transition: left 0.3s ease;
                z-index: 1050;
                height: 100%;
                width: 280px;
            }

            #sidebar.show {
                left: 0;
            }
        }

        /* ===== PAGINATION ===== */
        .pagination {
            justify-content: flex-end;
        }

        .page-link {
            color: #4c1d95;
        }

        .page-item.active .page-link {
            background-color: #4c1d95;
            border-color: #4c1d95;
        }

        /* ===== CUSTOM UTILITIES ===== */
        .btn-purple {
            background-color: #4c1d95;
            border-color: #4c1d95;
            color: white;
        }

        .btn-purple:hover {
            background-color: #3b156b;
            border-color: #3b156b;
            color: white;
        }

        .btn-outline-purple {
            color: #4c1d95;
            border-color: #4c1d95;
        }

        .btn-outline-purple:hover {
            background-color: #4c1d95;
            color: white;
        }

        .bg-purple-100 {
            background-color: #f3e8ff;
        }

        .text-purple-600 {
            color: #7c3aed;
        }

        .text-purple-700 {
            color: #6d28d9;
        }

        .text-purple-800 {
            color: #4c1d95;
        }

        .bg-purple-600 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .rounded-4 {
            border-radius: 1rem;
        }

        .rounded-5 {
            border-radius: 1.5rem;
        }

        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Badge Colors */
        .bg-success-100 {
            background-color: #dcfce7;
        }

        .text-success-800 {
            color: #166534;
        }

        .bg-warning-100 {
            background-color: #fef3c7;
        }

        .text-warning-800 {
            color: #92400e;
        }

        .bg-danger-100 {
            background-color: #fee2e2;
        }

        .text-danger-800 {
            color: #991b1b;
        }

        .bg-info-100 {
            background-color: #e0f2fe;
        }

        .text-info-800 {
            color: #075985;
        }

        /* Dropdown */
        .dropdown-item:active {
            background-color: #4c1d95;
        }

        /* Alert */
        .alert {
            border: none;
        }

        /* Table */
        .table > :not(caption) > * > * {
            padding: 1rem 0.75rem;
        }
    </style>
</head>

<body class="bg-light">

    <!-- SIDEBAR -->
    <aside
        class="sidebar-gradient text-white shadow-2xl overflow-y-auto sidebar-scroll transition-all duration-300 fixed h-full z-50"
        id="sidebar" style="width: 280px;">
        <div class="p-4 border-bottom border-white border-opacity-10">
            <div class="d-flex align-items-center gap-3">
                <img src="https://polmed.ac.id/wp-content/uploads/2014/04/logo-polmed-png.png" alt="POLMED Logo"
                    style="height: 40px; width: auto;">
                <div>
                    <h1 class="fs-4 fw-bold mb-0">SERVQUAL</h1>
                    <p class="text-white text-opacity-50 small mb-0">Super Admin Panel</p>
                </div>
            </div>
        </div>

        <nav class="mt-3 px-3 pb-5">
            <a href="{{ route('super.dashboard') }}"
                class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 {{ request()->routeIs('super.dashboard') ? 'nav-active' : 'hover-bg-white-10' }}"
                style="text-decoration: none; color: inherit;">
                <i class="bi bi-speedometer2 fs-5"></i>
                <span class="small fw-medium">Dashboard</span>
            </a>
            
            <!-- Manajemen User -->
            <div class="mt-2">
                <div class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 cursor-pointer"
                    onclick="toggleUserMenu()" style="color: inherit;">
                    <i class="bi bi-people fs-5"></i>
                    <span class="small fw-medium flex-grow-1">Manajemen User</span>
                    <i class="bi bi-chevron-down fs-6" id="userChevron"></i>
                </div>
                <div id="userSubmenu" class="ps-4" style="display: none;">
                    <a href="{{ route('super.users.index') }}"
                        class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 {{ request()->routeIs('super.users.*') ? 'nav-active' : 'hover-bg-white-10' }}"
                        style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person fs-6"></i>
                        <span class="small fw-medium">Semua User</span>
                    </a>
                </div>
            </div>

            <!-- Data Master -->
            <div class="mt-2">
                <div class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 cursor-pointer"
                    onclick="toggleMasterMenu()" style="color: inherit;">
                    <i class="bi bi-database fs-5"></i>
                    <span class="small fw-medium flex-grow-1">Data Master</span>
                    <i class="bi bi-chevron-down fs-6" id="masterChevron"></i>
                </div>
                <div id="masterSubmenu" class="ps-4" style="display: none;">
                    <a href="{{ route('super.jurusan.index') }}"
                        class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 {{ request()->routeIs('super.jurusan.*') ? 'nav-active' : 'hover-bg-white-10' }}"
                        style="text-decoration: none; color: inherit;">
                        <i class="bi bi-building fs-6"></i>
                        <span class="small fw-medium">Jurusan</span>
                    </a>
                    <a href="{{ route('super.prodi.index') }}"
                        class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 {{ request()->routeIs('super.prodi.*') ? 'nav-active' : 'hover-bg-white-10' }}"
                        style="text-decoration: none; color: inherit;">
                        <i class="bi bi-book fs-6"></i>
                        <span class="small fw-medium">Program Studi</span>
                    </a>
                    <a href="{{ route('super.periode.index') }}"
                        class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 {{ request()->routeIs('super.periode.*') ? 'nav-active' : 'hover-bg-white-10' }}"
                        style="text-decoration: none; color: inherit;">
                        <i class="bi bi-calendar-week fs-6"></i>
                        <span class="small fw-medium">Periode Kuesioner</span>
                    </a>
                </div>
            </div>

            <!-- Pengaturan Sistem -->
            <a href="{{ route('super.settings.index') }}"
                class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 {{ request()->routeIs('super.settings.*') ? 'nav-active' : 'hover-bg-white-10' }}"
                style="text-decoration: none; color: inherit;">
                <i class="bi bi-gear fs-5"></i>
                <span class="small fw-medium">Pengaturan Sistem</span>
            </a>

            <!-- Laporan -->
            <div class="mt-2">
                <div class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 cursor-pointer"
                    onclick="toggleLaporanMenu()" style="color: inherit;">
                    <i class="bi bi-file-text fs-5"></i>
                    <span class="small fw-medium flex-grow-1">Laporan</span>
                    <i class="bi bi-chevron-down fs-6" id="laporanChevron"></i>
                </div>
                <div id="laporanSubmenu" class="ps-4" style="display: none;">
                    <a href="{{ route('super.laporan.index') }}"
                        class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 {{ request()->routeIs('super.laporan.index') ? 'nav-active' : 'hover-bg-white-10' }}"
                        style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person-badge fs-6"></i>
                        <span class="small fw-medium">Penilaian Dosen</span>
                    </a>
                    <a href="{{ route('super.laporan.fasilitas') }}"
                        class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all mb-1 {{ request()->routeIs('super.laporan.fasilitas') ? 'nav-active' : 'hover-bg-white-10' }}"
                        style="text-decoration: none; color: inherit;">
                        <i class="bi bi-building fs-6"></i>
                        <span class="small fw-medium">Penilaian Fasilitas</span>
                    </a>
                </div>
            </div>
        </nav>

        <div class="position-absolute bottom-0 w-100 p-3 border-top border-white border-opacity-10"
            style="width: 280px;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="d-flex align-items-center gap-3 w-100 px-3 py-2 rounded-3 text-white text-opacity-75 bg-transparent border-0 transition-all hover-bg-white-10">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                    <span class="small fw-medium">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content" style="margin-left: 280px;">
        <!-- TOP NAVBAR -->
        <div class="bg-white shadow-sm sticky-top z-10">
            <div class="d-flex justify-content-between align-items-center px-4 py-2">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebarToggle" class="btn btn-link text-secondary d-lg-none p-0 border-0">
                        <i class="bi bi-list fs-3"></i>
                    </button>
                    <h2 class="fs-5 fw-semibold text-dark mb-0 d-none d-md-block">@yield('page_title', 'Dashboard')</h2>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <!-- Notifikasi -->
                    <div class="position-relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="btn btn-link text-secondary p-1 border-0 position-relative">
                            <i class="bi bi-bell fs-5"></i>
                            @php 
                                $unreadCount = isset($unreadCount) ? $unreadCount : 0; 
                            @endphp
                            @if($unreadCount > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 0.6rem;">{{ $unreadCount }}</span>
                            @endif
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="position-absolute end-0 mt-2 bg-white rounded-3 shadow-lg border"
                            style="width: 320px; display: none; z-index: 1050;">
                            <div class="py-2">
                                <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Notifikasi</span>
                                    @if($unreadCount > 0)
                                        <button onclick="markAllNotificationsRead()"
                                            class="btn btn-link btn-sm text-purple-600 p-0">Tandai semua dibaca</button>
                                    @endif
                                </div>
                                <div style="max-height: 400px; overflow-y: auto;">
                                    @forelse(($notifikasi ?? []) as $notif)
                                        <div class="px-3 py-2 border-bottom notif-item cursor-pointer hover-bg-light"
                                            data-id="{{ $notif->id }}">
                                            <p class="small text-dark mb-0">{{ $notif->message }}</p>
                                            <p class="small text-muted mt-1 mb-0">{{ $notif->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    @empty
                                        <div class="px-3 py-4 text-center text-muted small">Tidak ada notifikasi</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="position-relative" x-data="{ open: false }">
                        <button @click="open = !open" class="btn btn-link p-0 border-0 d-flex align-items-center gap-2">
                            <div class="bg-purple-100 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;">
                                <span class="text-purple-800 fw-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <span class="small fw-medium text-dark d-none d-md-block">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down text-secondary d-none d-md-block"></i>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="position-absolute end-0 mt-2 bg-white rounded-3 shadow-lg border"
                            style="width: 200px; display: none; z-index: 1050;">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item px-3 py-2 small">Profile</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item px-3 py-2 small text-danger">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3 rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3 rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="p-3 p-md-4">
            @yield('content')
        </div>
    </main>

    <!-- MOBILE BOTTOM NAVBAR -->
    <div class="mobile-nav d-lg-none">
        <a href="{{ route('super.dashboard') }}"
            class="nav-item {{ request()->routeIs('super.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('super.users.index') }}"
            class="nav-item {{ request()->routeIs('super.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span>Users</span>
        </a>
        <a href="{{ route('super.periode.index') }}"
            class="nav-item {{ request()->routeIs('super.periode.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-week"></i>
            <span>Periode</span>
        </a>
        <a href="{{ route('super.settings.index') }}"
            class="nav-item {{ request()->routeIs('super.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>
        <a href="{{ route('super.laporan.index') }}"
            class="nav-item {{ request()->routeIs('super.laporan.*') ? 'active' : '' }}">
            <i class="bi bi-file-text"></i>
            <span>Laporan</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="nav-item border-0 bg-transparent">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Axios CSRF Token
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Sidebar Toggle
        (function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle('show');
                });
            }

            document.addEventListener('click', function(event) {
                if (sidebar && sidebar.classList.contains('show') &&
                    !sidebar.contains(event.target) &&
                    event.target !== toggleBtn &&
                    !toggleBtn?.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            });
        })();

        // User Menu Toggle
        function toggleUserMenu() {
            const submenu = document.getElementById('userSubmenu');
            const chevron = document.getElementById('userChevron');
            
            if (submenu.style.display === 'none' || submenu.style.display === '') {
                submenu.style.display = 'block';
                if (chevron) chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
            } else {
                submenu.style.display = 'none';
                if (chevron) chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
            }
        }

        // Master Menu Toggle
        function toggleMasterMenu() {
            const submenu = document.getElementById('masterSubmenu');
            const chevron = document.getElementById('masterChevron');
            
            if (submenu.style.display === 'none' || submenu.style.display === '') {
                submenu.style.display = 'block';
                if (chevron) chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
            } else {
                submenu.style.display = 'none';
                if (chevron) chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
            }
        }

        // Laporan Menu Toggle
        function toggleLaporanMenu() {
            const submenu = document.getElementById('laporanSubmenu');
            const chevron = document.getElementById('laporanChevron');
            
            if (submenu.style.display === 'none' || submenu.style.display === '') {
                submenu.style.display = 'block';
                if (chevron) chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
            } else {
                submenu.style.display = 'none';
                if (chevron) chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
            }
        }

        // Cek route aktif untuk membuka submenu yang sesuai
        if (window.location.href.includes('/super-admin/users')) {
            const submenu = document.getElementById('userSubmenu');
            const chevron = document.getElementById('userChevron');
            if (submenu) submenu.style.display = 'block';
            if (chevron) chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
        }
        
        if (window.location.href.includes('/super-admin/jurusan') || 
            window.location.href.includes('/super-admin/prodi') || 
            window.location.href.includes('/super-admin/periode')) {
            const submenu = document.getElementById('masterSubmenu');
            const chevron = document.getElementById('masterChevron');
            if (submenu) submenu.style.display = 'block';
            if (chevron) chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
        }
        
        if (window.location.href.includes('/super-admin/laporan')) {
            const submenu = document.getElementById('laporanSubmenu');
            const chevron = document.getElementById('laporanChevron');
            if (submenu) submenu.style.display = 'block';
            if (chevron) chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
        }

        // Notifikasi
        document.querySelectorAll('.notif-item').forEach(el => {
            el.addEventListener('click', function() {
                let id = this.dataset.id;
                if (!id) return;
                axios.post('/super-admin/notifikasi/' + id + '/read')
                    .then(() => location.reload())
                    .catch(err => console.error(err));
            });
        });

        function markAllNotificationsRead() {
            axios.post('/super-admin/notifikasi/read-all')
                .then(() => location.reload())
                .catch(err => console.error(err));
        }

        // Hover class
        document.querySelectorAll('.hover-bg-white-10').forEach(el => {
            el.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
            });
            el.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });

        document.querySelectorAll('.hover-bg-light').forEach(el => {
            el.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });
            el.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    </script>
    @stack('scripts')
</body>

</html>