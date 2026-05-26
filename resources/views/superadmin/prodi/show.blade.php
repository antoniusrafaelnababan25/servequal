@extends('layouts.superadmin')

@section('title', 'Detail Program Studi - Super Admin')
@section('page_title', 'Detail Program Studi')

@section('content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super.prodi.index') }}"
                                    class="text-purple">Program Studi</a></li>
                            <li class="breadcrumb-item active">Detail Program Studi</li>
                        </ol>
                    </nav>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-purple-100 rounded-circle p-3 me-3">
                                <i class="bi bi-book text-purple-600 fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-0">Detail Program Studi</h5>
                                <p class="text-muted small mb-0">Informasi lengkap program studi</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('super.prodi.edit', $prodi->id) }}"
                                class="btn btn-purple rounded-pill px-4 me-2">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                            <a href="{{ route('super.prodi.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card border-0 bg-light rounded-4 overflow-hidden">
                        <div class="card-header bg-purple-100 border-0 py-3">
                            <h6 class="fw-semibold mb-0 text-purple-800">
                                <i class="bi bi-card-list me-2"></i>Informasi Program Studi
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-hash me-1"></i>ID Prodi
                                    </label>
                                    <p class="fw-semibold fs-5 mb-0">#{{ $prodi->id }}</p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-tag me-1"></i>Slug
                                    </label>
                                    <p class="fw-semibold mb-0">{{ $prodi->slug }}</p>
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-building me-1"></i>Jurusan
                                    </label>
                                    <p class="fw-semibold mb-0">{{ $prodi->jurusan->nama_jurusan ?? '-' }}</p>
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-book me-1"></i>Nama Program Studi
                                    </label>
                                    <p class="fw-semibold fs-5 mb-0">{{ $prodi->nama_prodi }}</p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-bar-chart me-1"></i>Jenjang
                                    </label>
                                    <p class="fw-semibold mb-0">{{ ucfirst($prodi->jenjang) }}</p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-activity me-1"></i>Status
                                    </label>
                                    @if($prodi->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                            <i class="bi bi-x-circle-fill me-1"></i>Nonaktif
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-calendar-plus me-1"></i>Tanggal Dibuat
                                    </label>
                                    <p class="mb-0">
                                        {{ $prodi->created_at ? $prodi->created_at->format('d/m/Y H:i:s') : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-calendar-check me-1"></i>Terakhir Update
                                    </label>
                                    <p class="mb-0">
                                        {{ $prodi->updated_at ? $prodi->updated_at->format('d/m/Y H:i:s') : '-' }}</p>
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
            color: #7c3aed;
        }

        .text-purple-800 {
            color: #4c1d95;
        }

        .bg-purple-100 {
            background-color: #f3e8ff;
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