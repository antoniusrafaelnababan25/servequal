<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SERVQUAL POLMED</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
        }

        /* Split Screen Layout */
        .split-screen {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* Left Side - Branding */
        .left-side {
            flex: 1;
            background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 50%, #2e1065 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background Pattern */
        .left-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.08"><path fill="white" d="M10,10 L90,10 L90,90 L10,90 Z" stroke="white" stroke-width="0.5"/><circle cx="20" cy="20" r="2" fill="white"/><circle cx="50" cy="50" r="2" fill="white"/><circle cx="80" cy="80" r="2" fill="white"/><circle cx="20" cy="80" r="2" fill="white"/><circle cx="80" cy="20" r="2" fill="white"/></svg>');
            background-repeat: repeat;
            animation: movePattern 30s linear infinite;
        }

        @keyframes movePattern {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Floating Circles Animation */
        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .circle-1 { width: 300px; height: 300px; top: -100px; right: -100px; animation-delay: 0s; }
        .circle-2 { width: 200px; height: 200px; bottom: 10%; left: -50px; animation-delay: 2s; }
        .circle-3 { width: 150px; height: 150px; top: 30%; right: 20%; animation-delay: 4s; }
        .circle-4 { width: 100px; height: 100px; bottom: 20%; right: 30%; animation-delay: 6s; }
        .circle-5 { width: 80px; height: 80px; top: 15%; left: 15%; animation-delay: 3s; }

        .left-content {
            position: relative;
            z-index: 10;
            text-align: center;
            color: white;
            padding: 2rem;
            max-width: 500px;
        }

        /* Logo Styling */
        .logo-wrapper {
            margin-bottom: 2rem;
        }

        .logo-img {
            width: 100px;
            height: auto;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .logo-text {
            margin-top: 1rem;
        }

        .logo-text h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #ffffff 0%, #e9d5ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-text .tagline {
            font-size: 0.85rem;
            opacity: 0.8;
            letter-spacing: 2px;
        }

        .left-content .description {
            font-size: 0.95rem;
            line-height: 1.6;
            opacity: 0.9;
            margin: 1.5rem 0;
        }

        .feature-list {
            text-align: left;
            display: inline-block;
            margin: 0 auto;
        }

        .feature-list li {
            margin-bottom: 0.75rem;
            list-style: none;
            font-size: 0.9rem;
        }

        .feature-list li i {
            margin-right: 0.75rem;
            color: #c084fc;
        }

        /* Right Side - Login Form */
        .right-side {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            padding: 2rem;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Form Styling */
        .input-group-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group-custom .form-control {
            border: 2px solid #e9ecef;
            border-radius: 0.75rem;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .input-group-custom .form-control:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25);
            background: white;
        }

        .input-group-custom .form-control.is-invalid {
            border-color: #dc2626;
        }

        .input-group-custom .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            z-index: 10;
        }

        .input-group-custom .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            cursor: pointer;
            z-index: 10;
            transition: color 0.3s ease;
        }

        .input-group-custom .toggle-password:hover {
            color: #7c3aed;
        }

        /* Button Styling */
        .btn-login {
            background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 0.85rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
            color: white;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(76, 29, 149, 0.4);
        }

        .btn-login:disabled {
            opacity: 0.7;
            transform: none;
        }

        /* Checkbox & Links */
        .form-check-input:checked {
            background-color: #7c3aed;
            border-color: #7c3aed;
        }

        .forgot-link {
            color: #7c3aed;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #4c1d95;
            text-decoration: underline;
        }

        /* Alert */
        .alert-custom {
            border-radius: 0.75rem;
            border: none;
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }

        /* POLMED Watermark */
        .polmed-watermark {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.7rem;
            opacity: 0.5;
            color: white;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .split-screen {
                flex-direction: column;
            }
            .left-side {
                display: none;
            }
            .right-side {
                flex: 1;
            }
            body {
                background: white;
            }
        }

        /* Animation for form */
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

        .login-container {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>

<div class="split-screen">
    <!-- LEFT SIDE - BRANDING -->
    <div class="left-side">
        <div class="floating-circle circle-1"></div>
        <div class="floating-circle circle-2"></div>
        <div class="floating-circle circle-3"></div>
        <div class="floating-circle circle-4"></div>
        <div class="floating-circle circle-5"></div>
        
        <div class="left-content">
            <div class="logo-wrapper">
                <!-- Logo POLMED -->
                <img src="https://polmed.ac.id/wp-content/uploads/2014/04/logo-polmed-png.png" alt="POLMED Logo" class="logo-img">
                <div class="logo-text">
                    <h1>SERVQUAL</h1>
                    <div class="tagline">Monitoring System</div>
                </div>
            </div>
            
            <p class="description">
                Sistem monitoring kualitas layanan berbasis metode SERVQUAL untuk meningkatkan kepuasan stakeholder POLMED.
            </p>
        </div>
        
        <!-- POLMED Watermark -->
        <div class="polmed-watermark">
            POLITEKNIK NEGERI MEDAN
        </div>
    </div>

    <!-- RIGHT SIDE - LOGIN FORM -->
    <div class="right-side">
        <div class="login-container">
            <div class="login-header">
                <h2>Selamat Datang Kembali</h2>
                <p>Silakan login untuk melanjutkan ke dashboard</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success alert-custom">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-custom">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email Address -->
                <div class="input-group-custom">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Alamat Email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="input-group-custom">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Password" required>
                    <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
                    @error('password')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="remember_me">
                            Ingat Saya
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            Lupa Password?
                        </a>
                    @endif
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle Password Visibility
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    }

    // Form submission with loading effect
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');

    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            if (loginBtn) {
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
            }
        });
    }

    // Tampilkan error dari session dengan SweetAlert
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '{{ $errors->first() }}',
            confirmButtonColor: '#7c3aed',
            background: '#fff',
            customClass: {
                popup: 'rounded-4'
            }
        });
    @endif
</script>
</body>
</html>