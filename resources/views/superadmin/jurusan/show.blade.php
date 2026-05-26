@extends('layouts.superadmin')

@section('title', 'Detail Jurusan - Super Admin')
@section('page_title', 'Detail Jurusan')

@section('content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super.jurusan.index') }}"
                                    class="text-purple">Jurusan</a></li>
                            <li class="breadcrumb-item active">Detail Jurusan</li>
                        </ol>
                    </nav>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-purple-100 rounded-circle p-3 me-3">
                                <i class="bi bi-building text-purple-600 fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-0">Detail Jurusan</h5>
                                <p class="text-muted small mb-0">Informasi lengkap jurusan</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('super.jurusan.edit', $jurusan->id) }}"
                                class="btn btn-purple rounded-pill px-4 me-2">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                            <a href="{{ route('super.jurusan.index') }}"
                                class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card border-0 bg-light rounded-4 overflow-hidden">
                        <div class="card-header bg-purple-100 border-0 py-3">
                            <h6 class="fw-semibold mb-0 text-purple-800">
                                <i class="bi bi-card-list me-2"></i>Informasi Jurusan
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-hash me-1"></i>ID Jurusan
                                    </label>
                                    <p class="fw-semibold fs-5 mb-0">#{{ $jurusan->id }}</p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-tag me-1"></i>Slug
                                    </label>
                                    <p class="fw-semibold mb-0">{{ $jurusan->slug }}</p>
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-building me-1"></i>Nama Jurusan
                                    </label>
                                    <p class="fw-semibold fs-5 mb-0">{{ $jurusan->nama_jurusan }}</p>
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-file-text me-1"></i>Deskripsi
                                    </label>
                                    <div class="p-3 bg-white rounded-3 border">
                                        <p class="mb-0">{{ $jurusan->deskripsi ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-calendar-plus me-1"></i>Tanggal Dibuat
                                    </label>
                                    <p class="mb-0">
                                        {{ $jurusan->created_at ? $jurusan->created_at->format('d/m/Y H:i:s') : '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-calendar-check me-1"></i>Terakhir Update
                                    </label>
                                    <p class="mb-0">
                                        {{ $jurusan->updated_at ? $jurusan->updated_at->format('d/m/Y H:i:s') : '-' }}</p>
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