<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SERVQUAL - POLMED')</title>

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Card Styling */
        .card-kuesioner {
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        /* Button Purple */
        .btn-purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-purple:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .btn-purple:disabled {
            opacity: 0.6;
            transform: none;
        }

        /* Button Outline Purple */
        .btn-outline-purple {
            border: 2px solid #667eea;
            background: transparent;
            color: #667eea;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-purple:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }

        /* Form Controls */
        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Logo */
        .logo {
            max-height: 70px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        /* Footer */
        footer {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Accordion */
        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0, 0, 0, 0.125);
        }

        /* Progress Steps */
        .progress-step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #666;
        }

        .progress-step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .progress-step.completed {
            background: #28a745;
            color: white;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #e0e0e0;
        }

        /* Rating Star */
        .rating-star {
            cursor: pointer;
            font-size: 1.5rem;
            transition: all 0.2s;
        }

        .rating-star:hover {
            transform: scale(1.1);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .card-kuesioner {
                padding: 1rem !important;
            }

            .progress-step {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="container py-4">
        <div class="text-center mb-4 animate-fade-in-up">
            <img src="https://polmed.ac.id/wp-content/uploads/2014/04/logo-polmed-png.png" alt="POLMED"
                class="logo mb-3">
            <h1 class="text-white fw-bold">Sistem Monitoring</h1>
            <p class="text-white opacity-75">Evaluasi Layanan Akademik Politeknik Negeri Medan</p>
        </div>

        @yield('content')

        <footer class="text-center mt-5 pt-3">
            <p>&copy; {{ date('Y') }} Politeknik Negeri Medan | All Rights Reserved</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Axios Configuration -->
    <script>
        // Set default CSRF token for all Axios requests
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        // Add response interceptor for debugging
        axios.interceptors.response.use(
            function (response) {
                return response;
            },
            function (error) {
                console.error('Axios Error:', error.response?.data || error.message);
                return Promise.reject(error);
            }
        );
    </script>

    @stack('scripts')
</body>

</html>