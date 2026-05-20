@extends('layouts.public')

@section('title', 'Terima Kasih - SERVQUAL POLMED')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-kuesioner p-5 text-center animate-fade-in-up">
                <div class="mb-4">
                    <div class="bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-check-lg text-white fs-1"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-3">Terima Kasih!</h3>
                <p class="text-muted mb-4">Penilaian Anda telah kami terima. Partisipasi Anda sangat berharga untuk peningkatan kualitas layanan akademik.</p>
                <div class="d-flex gap-3 justify-center">
                    <a href="{{ route('public.validasi.form') }}" class="btn-outline-purple px-4 py-2 text-decoration-none">
                        <i class="bi bi-arrow-repeat me-2"></i>Kuesioner Lagi
                    </a>
                    <a href="{{ url('/') }}" class="btn-purple px-4 py-2 text-decoration-none">
                        <i class="bi bi-house me-2"></i>Ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection