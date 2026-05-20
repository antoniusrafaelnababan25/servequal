@extends('layouts.public')

@section('title', 'Kuesioner Ditutup - SERVQUAL POLMED')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-kuesioner p-5 text-center animate-fade-in-up">
                <div class="mb-4">
                    <div class="bg-danger bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-x-lg text-white fs-1"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-3">Kuesioner Sedang Ditutup</h3>
                <p class="text-muted mb-4">
                    {{ $message ?? 'Kuesioner belum tersedia untuk periode ini. Silakan cek kembali nanti.' }}</p>
                <a href="{{ url('/') }}" class="btn-purple px-4 py-2 text-decoration-none">
                    <i class="bi bi-house me-2"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
@endsection