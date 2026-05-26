@extends('layouts.admin')

@section('title', 'Detail Pertanyaan - Admin')
@section('page_title', 'Detail Pertanyaan')

@section('content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.pertanyaan.index') }}" class="text-purple text-decoration-none">
                                    <i class="bi bi-question-circle me-1"></i>Pertanyaan
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </nav>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-purple bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-info-circle-fill text-purple-600 fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-0">Detail Pertanyaan</h5>
                                <p class="text-muted small mb-0">Informasi lengkap pertanyaan</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.pertanyaan.edit', $pertanyaan->id) }}"
                                class="btn btn-purple rounded-pill px-4 me-2">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                            <a href="{{ route('admin.pertanyaan.index') }}"
                                class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card border-0 bg-light rounded-4 overflow-hidden">
                        <div class="card-header bg-purple bg-opacity-10 border-0 py-3">
                            <h6 class="fw-semibold mb-0 text-purple-600">
                                <i class="bi bi-card-list me-2"></i>Informasi Pertanyaan
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-hash me-1"></i>ID Pertanyaan
                                    </label>
                                    <p class="fw-semibold fs-5 mb-0">#{{ $pertanyaan->id }}</p>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-activity me-1"></i>Status
                                    </label>
                                    @if($pertanyaan->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                            <i class="bi bi-x-circle-fill me-1"></i>Nonaktif
                                        </span>
                                    @endif
                                </div>

                                <div class="col-12 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-chat-text me-1"></i>Teks Pertanyaan
                                    </label>
                                    <div class="p-3 bg-white rounded-3 border">
                                        <p class="mb-0 fs-5">{{ $pertanyaan->teks }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-tag me-1"></i>Tipe Penilaian
                                    </label>
                                    @if($pertanyaan->tipe_penilaian == 'penilaian_dosen')
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                            <i class="bi bi-person-badge me-1"></i>Penilaian Dosen
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                            <i class="bi bi-building me-1"></i>Penilaian Fasilitas
                                        </span>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-person me-1"></i>Target Responden
                                    </label>
                                    {!! $pertanyaan->target_role_badge !!}
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-info-circle"></i>
                                        @if($pertanyaan->target_role == 'mahasiswa')
                                            Pertanyaan ini hanya diisi oleh Mahasiswa
                                        @elseif($pertanyaan->target_role == 'dosen')
                                            Pertanyaan ini hanya diisi oleh Dosen
                                        @else
                                            Pertanyaan ini diisi oleh Mahasiswa dan Dosen
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-diagram-3 me-1"></i>Dimensi
                                    </label>
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                                        <i class="bi bi-diagram-3 me-1"></i>{{ $pertanyaan->dimensi }}
                                    </span>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-building me-1"></i>Kategori Fasilitas
                                    </label>
                                    @if($pertanyaan->kategori_fasilitas)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                            {{ ucfirst($pertanyaan->kategori_fasilitas) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-calendar-plus me-1"></i>Tanggal Dibuat
                                    </label>
                                    <p class="mb-0">
                                        {{ $pertanyaan->created_at ? $pertanyaan->created_at->format('d/m/Y H:i:s') : '-' }}
                                    </p>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-calendar-check me-1"></i>Terakhir Diupdate
                                    </label>
                                    <p class="mb-0">
                                        {{ $pertanyaan->updated_at ? $pertanyaan->updated_at->format('d/m/Y H:i:s') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .text-purple-600 {
            color: #4c1d95;
        }

        .btn-purple {
            background-color: #4c1d95;
            border-color: #4c1d95;
            color: white;
        }

        .btn-purple:hover {
            background-color: #3b1580;
            border-color: #3b1580;
            color: white;
        }
    </style>
@endsection