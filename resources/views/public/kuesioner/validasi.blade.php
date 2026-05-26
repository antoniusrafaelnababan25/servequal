@extends('layouts.public')

@section('title', 'Validasi Mahasiswa - SERVQUAL POLMED')

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Montserrat', system-ui, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -20%;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .main-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 0.8rem 2rem;
            border-radius: 100px;
            margin-bottom: 1rem;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }

        .logo-sub {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.3rem;
        }

        .title-section {
            text-align: center;
            margin-bottom: 1rem;
        }

        .title-section h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .title-section p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .info-badge {
            text-align: center;
            margin-bottom: 2rem;
        }

        .info-badge span {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 0.4rem 1.2rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.95);
        }

        .form-wrapper {
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border-radius: 28px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
        }

        .form-label i {
            margin-right: 0.3rem;
        }

        .form-input {
            width: 100%;
            padding: 0.9rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #a855f7;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.2);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        input[type="date"] {
            color-scheme: dark;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            border: none;
            border-radius: 14px;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            box-shadow: 0 10px 30px rgba(109, 40, 217, 0.4);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.7rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .divider span {
            margin: 0 1rem;
            font-weight: 600;
        }

        .login-link {
            text-align: center;
        }

        .login-link a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.8rem;
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            border-radius: 14px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            border-color: #a855f7;
            background: rgba(168, 85, 247, 0.1);
            transform: translateY(-2px);
            color: white;
        }

        .info-note {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .info-note i {
            color: #c084fc;
            margin-right: 0.3rem;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 14px;
            color: #fca5a5;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.8rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .stats-row {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 0.8rem 1.2rem;
            min-width: 100px;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.1);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #c084fc, #a855f7);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .stat-label {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 0.2rem;
        }

        .footer {
            text-align: center;
            padding: 1rem;
            width: 100%;
            background: transparent;
            margin-top: 2rem;
        }

        .footer p {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.7rem;
            margin: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .main-container {
            animation: fadeInUp 0.6s ease-out;
        }

        @media (max-width: 768px) {
            .stats-row {
                gap: 1rem;
            }

            .stat-item {
                padding: 0.5rem 0.8rem;
                min-width: 70px;
            }

            .stat-value {
                font-size: 1.2rem;
            }

            .title-section h1 {
                font-size: 1.5rem;
            }

            .form-wrapper {
                padding: 1.5rem;
            }
        }
    </style>

    <div class="main-container">
        <div class="form-wrapper">
            @if(session('error'))
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('public.validasi') }}" id="validasiForm">
                @csrf
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-credit-card"></i> NIM
                    </label>
                    <input type="text" name="nim" class="form-input" placeholder="Contoh: 2024123456"
                        value="{{ old('nim') }}" required autofocus maxlength="20">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-calendar-date"></i> Tanggal Lahir
                    </label>
                    <input type="date" name="tanggal_lahir" class="form-input" value="{{ old('tanggal_lahir') }}" required>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="bi bi-arrow-right-circle"></i> Lanjutkan ke Kuesioner
                </button>
            </form>

            <div class="divider">
                <span>atau</span>
            </div>

            <div class="login-link">
                <a href="{{ route('login') }}">
                    <i class="bi bi-box-arrow-in-right"></i> Login sebagai Admin / Dosen
                </a>
            </div>

            <div class="info-note">
                <i class="bi bi-info-circle"></i> Mahasiswa cukup memasukkan NIM dan Tanggal Lahir
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-value">{{ number_format($totalDosen ?? 0) }}</div>
                <div class="stat-label">Dosen</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($totalMahasiswa ?? 0) }}</div>
                <div class="stat-label">Mahasiswa</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($totalPenilaian ?? 0) }}</div>
                <div class="stat-label">Penilaian</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($rataKepuasan ?? 0, 1) }}/5</div>
                <div class="stat-label">Kepuasan</div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} SERVQUAL POLMED. All rights reserved.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('validasiForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form) {
                form.addEventListener('submit', function (e) {
                    const nimInput = document.querySelector('input[name="nim"]');
                    const tanggalInput = document.querySelector('input[name="tanggal_lahir"]');

                    if (nimInput && !nimInput.value.trim()) {
                        e.preventDefault();
                        Swal.fire('Error', 'NIM harus diisi', 'error');
                        nimInput.focus();
                        return;
                    }

                    if (nimInput && nimInput.value.trim().length < 10) {
                        e.preventDefault();
                        Swal.fire('Error', 'NIM minimal 10 digit', 'error');
                        nimInput.focus();
                        return;
                    }

                    if (tanggalInput && !tanggalInput.value) {
                        e.preventDefault();
                        Swal.fire('Error', 'Tanggal lahir harus diisi', 'error');
                        tanggalInput.focus();
                        return;
                    }

                    if (!e.defaultPrevented) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
                    }
                });
            }
        });
    </script>
@endsection