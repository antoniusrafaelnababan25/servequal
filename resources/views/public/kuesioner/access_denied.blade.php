<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - SERVQUAL POLMED</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .denied-card {
            background: white;
            border-radius: 2rem;
            padding: 3rem 2rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.6s ease-out;
        }

        .denied-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .denied-icon i {
            font-size: 3.5rem;
            color: white;
        }

        .denied-card h2 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #dc2626;
            margin-bottom: 1rem;
        }

        .denied-card p {
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .info-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 1rem;
            padding: 1rem;
            margin: 1.5rem 0;
        }

        .info-box .date {
            font-size: 1.2rem;
            font-weight: 700;
            color: #dc2626;
        }

        .info-box .message {
            font-size: 0.9rem;
            color: #991b1b;
            margin-top: 0.5rem;
        }

        .btn-contact {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #4c1d95, #7c3aed);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .btn-contact:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(76, 29, 149, 0.4);
            color: white;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: transparent;
            border: 1.5px solid #4c1d95;
            color: #4c1d95;
            padding: 0.8rem 1.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-back:hover {
            background: #4c1d95;
            color: white;
        }

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

        @media (max-width: 768px) {
            .denied-card {
                padding: 2rem 1.5rem;
            }

            .denied-card h2 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>

<body>
    <div class="denied-card">
        <div class="denied-icon">
            <i class="bi bi-lock-fill"></i>
        </div>

        <h2>⚠️ Akses Ditolak</h2>

        <p>
            Maaf, akses Anda ke sistem kuesioner tidak dapat dilanjutkan.
        </p>

        <div class="info-box">
            <i class="bi bi-calendar-exclamation"></i>
            <div class="date">Masa berlaku: {{ $expiryDate }}</div>
            <div class="message">
                <i class="bi bi-info-circle me-1"></i>
                Sistem ini hanya dapat diakses hingga tanggal tersebut, Hubungi Bg Pael Pigi Langsung Ke Kos nya Dan
                Bayar.
            </div>
        </div>

        <p class="small text-muted">
            <i class="bi bi-envelope me-1"></i>
            Untuk informasi lebih lanjut, silakan hubungi administrator:
        </p>

        <div>
            <a href="mailto:admin@polmed.ac.id" class="btn-contact">
                <i class="bi bi-envelope-fill"></i> Hubungi Admin
            </a>
        </div>

        <div>
            <a href="{{ url('/') }}" class="btn-back">
                <i class="bi bi-house-door"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</body>

</html>