<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Profile | SERVQUAL POLMED')</title>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .profile-card {
            background: white;
            border-radius: 2rem;
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.2);
        }

        .avatar-circle {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .avatar-circle i {
            font-size: 3rem;
            color: white;
        }

        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4c1d95;
            box-shadow: 0 0 0 0.2rem rgba(76, 29, 149, 0.25);
        }

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

        .text-purple-800 {
            color: #4c1d95;
        }

        .rounded-4 {
            border-radius: 1rem;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            padding: 12px 24px;
            font-weight: 500;
        }

        .nav-tabs .nav-link:hover {
            color: #4c1d95;
            border: none;
        }

        .nav-tabs .nav-link.active {
            color: #4c1d95;
            border-bottom: 3px solid #4c1d95;
            background: transparent;
        }

        .role-badge {
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="text-center mb-4">
                    @php
                        $dashboardRoute = match (Auth::user()->role) {
                            'super_admin' => route('super.dashboard'),
                            'admin' => route('admin.dashboard'),
                            'dosen' => route('dosen.dashboard'),
                            default => route('dashboard'),
                        };
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="btn btn-light rounded-pill px-4 mb-3">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>