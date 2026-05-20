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
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Main Container - Langsung di tengah tanpa card */
    .main-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 2rem;
    }

    /* Logo */
    .logo {
        text-align: center;
        margin-bottom: 2rem;
    }

    .logo img {
        height: 70px;
        width: auto;
        filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.2));
    }

    /* Title */
    .title {
        text-align: center;
        margin-bottom: 0.5rem;
    }

    .title h1 {
        font-size: 2rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .title p {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
    }

    /* Badge */
    .badge {
        text-align: center;
        margin-bottom: 2rem;
    }

    .badge span {
        display: inline-block;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 0.3rem 1rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.9);
    }

    /* Form Wrapper - DITENGAHKAN */
    .form-wrapper {
        max-width: 400px;
        width: 100%;
        margin: 0 auto;
    }

    /* Form Group */
    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0.4rem;
    }

    .form-input {
        width: 100%;
        padding: 0.9rem 1rem;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        color: white;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        outline: none;
    }

    .form-input:focus {
        border-color: #c084fc;
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 0 3px rgba(192, 132, 252, 0.2);
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

    /* Button */
    .btn-submit {
        width: 100%;
        padding: 0.9rem;
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        border: none;
        border-radius: 12px;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 0.5rem;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #7c3aed, #5b21b6);
        box-shadow: 0 10px 25px rgba(109, 40, 217, 0.4);
    }

    /* Divider */
    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.2rem 0;
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
    }

    /* Link Login */
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
        border-radius: 12px;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .login-link a:hover {
        border-color: #c084fc;
        background: rgba(192, 132, 252, 0.1);
        transform: translateY(-2px);
        color: white;
    }

    /* Info Note */
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

    /* Alert Error */
    .alert-error {
        background: rgba(239, 68, 68, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 12px;
        color: #fca5a5;
        padding: 0.7rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.8rem;
        text-align: center;
    }

    /* Statistik Baris - HANYA SATU BARIS DI BAWAH FORM */
    .stats-row {
        display: flex;
        justify-content: center;
        gap: 3rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .stat-item {
        text-align: center;
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

    /* Footer SEDERHANA */
    .footer {
        text-align: center;
        padding: 1rem;
        width: 100%;
        background: transparent;
    }

    .footer p {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.7rem;
        margin: 0;
    }

    /* Animasi */
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

    /* Responsive */
    @media (max-width: 768px) {
        .stats-row {
            gap: 1.5rem;
        }

        .stat-value {
            font-size: 1.3rem;
        }

        .title h1 {
            font-size: 1.5rem;
        }

        .logo img {
            height: 50px;
        }
    }
</style>

<!-- Main Container - Langsung di tengah -->
<div class="main-container">


    <!-- Form - DI TENGAH -->
    <div class="form-wrapper">
        @if(session('error'))
        <div class="alert-error">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('public.validasi') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">NIM</label>
                <input type="text" name="nim" class="form-input" placeholder="Masukkan NIM Anda"
                    value="{{ old('nim') }}" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-input" value="{{ old('tanggal_lahir') }}" required>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-arrow-right-circle me-2"></i> Lanjutkan
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
            <i class="bi bi-info-circle"></i> Mahasiswa cukup masukkan NIM dan Tanggal Lahir
        </div>
    </div>

    <!-- Statistik - Hanya satu baris -->
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
</div>




@endsection