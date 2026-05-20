@extends('layouts.dosen')

@section('title', 'Terima Kasih - Kuesioner Fasilitas')
@section('page_title', 'Terima Kasih')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="bg-white rounded-4 shadow-sm p-5 text-center">
                    <!-- Icon -->
                    <div class="bg-success bg-opacity-10 rounded-circle p-4 d-inline-flex mx-auto mb-4">
                        <i class="bi bi-check-lg text-success fs-1"></i>
                    </div>

                    <h3 class="fw-bold mb-3">Terima Kasih!</h3>

                    <p class="text-muted mb-4">
                        Kuesioner fasilitas kampus telah berhasil kami terima.<br>
                        Apresiasi Anda sangat berharga untuk peningkatan kualitas layanan POLMED.
                    </p>

                    <div class="bg-light rounded-3 p-3 mb-4">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Jawaban Anda telah tersimpan dan akan digunakan untuk evaluasi fasilitas kampus.
                        </p>
                    </div>

                    <a href="{{ route('dosen.dashboard') }}" class="btn btn-purple rounded-pill px-5">
                        <i class="bi bi-house-door me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection