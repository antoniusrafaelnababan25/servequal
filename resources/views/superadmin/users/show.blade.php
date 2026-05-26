@extends('layouts.superadmin')

@section('title', 'Detail User - Super Admin')
@section('page_title', 'Detail User')

@section('content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('super.users.index') }}" class="text-purple text-decoration-none">
                                    <i class="bi bi-people me-1"></i>Manajemen User
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detail User</li>
                        </ol>
                    </nav>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-purple-100 rounded-circle p-3 me-3">
                                <i class="bi bi-person-circle text-purple-600 fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-0">Detail User</h5>
                                <p class="text-muted small mb-0">Informasi lengkap data user</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('super.users.edit', $user->id) }}"
                                class="btn btn-purple rounded-pill px-4 me-2">
                                <i class="bi bi-pencil me-2"></i>Edit
                            </a>
                            <a href="{{ route('super.users.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card border-0 bg-light rounded-4 overflow-hidden">
                        <div class="card-header bg-purple-100 border-0 py-3">
                            <h6 class="fw-semibold mb-0 text-purple-800">
                                <i class="bi bi-card-list me-2"></i>Informasi User
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-hash me-1"></i>ID User
                                    </label>
                                    <p class="fw-semibold fs-5 mb-0">#{{ $user->id }}</p>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-activity me-1"></i>Status
                                    </label>
                                    @if($user->is_active)
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
                                        <i class="bi bi-person me-1"></i>Nama Lengkap
                                    </label>
                                    <p class="fw-semibold mb-0">{{ $user->name }}</p>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-person-badge me-1"></i>Username
                                    </label>
                                    <p class="fw-semibold mb-0">{{ $user->username ?? '-' }}</p>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </label>
                                    <p class="fw-semibold mb-0">{{ $user->email }}</p>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-tag me-1"></i>Role
                                    </label>
                                    @php
                                        $roleColors = [
                                            'super_admin' => 'danger',
                                            'admin' => 'purple',
                                            'dosen' => 'success',
                                            'mahasiswa' => 'info'
                                        ];
                                        $color = $roleColors[$user->role] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}-100 text-{{ $color }}-800 rounded-pill px-3 py-2">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </div>

                                @if($user->role == 'dosen')
                                    <div class="col-md-6 mb-4">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="bi bi-card-text me-1"></i>NIDN
                                        </label>
                                        <p class="fw-semibold mb-0">{{ $user->nidn ?? '-' }}</p>
                                    </div>
                                @endif

                                @if($user->role == 'mahasiswa')
                                    <div class="col-md-6 mb-4">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="bi bi-card-text me-1"></i>NIM
                                        </label>
                                        <p class="fw-semibold mb-0">{{ $user->nim ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="bi bi-building me-1"></i>Kelas
                                        </label>
                                        <p class="fw-semibold mb-0">{{ $user->kelas ?? '-' }}</p>
                                    </div>
                                @endif

                                @if(in_array($user->role, ['dosen', 'mahasiswa']))
                                    <div class="col-md-6 mb-4">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="bi bi-building me-1"></i>Jurusan
                                        </label>
                                        <p class="fw-semibold mb-0">
                                            @if($user->jurusan)
                                                {{ $user->jurusan }}
                                            @elseif($user->prodi && $user->prodi->jurusan)
                                                {{ $user->prodi->jurusan->nama_jurusan }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="bi bi-book me-1"></i>Program Studi
                                        </label>
                                        <p class="fw-semibold mb-0">{{ $user->prodi->nama_prodi ?? '-' }}</p>
                                    </div>
                                @endif

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-calendar-date me-1"></i>Tanggal Lahir
                                    </label>
                                    <p class="fw-semibold mb-0">
                                        {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d/m/Y') : '-' }}
                                    </p>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-calendar-plus me-1"></i>Tanggal Dibuat
                                    </label>
                                    <p class="fw-semibold mb-0">
                                        {{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : '-' }}</p>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                        <i class="bi bi-calendar-check me-1"></i>Terakhir Update
                                    </label>
                                    <p class="fw-semibold mb-0">
                                        {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i:s') : '-' }}</p>
                                </div>

                                @if($user->last_login)
                                    <div class="col-md-6 mb-4">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-2">
                                            <i class="bi bi-clock-history me-1"></i>Terakhir Login
                                        </label>
                                        <p class="fw-semibold mb-0">
                                            {{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d/m/Y H:i:s') : '-' }}
                                        </p>
                                    </div>
                                @endif
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

        .bg-purple-100 {
            background-color: #f3e8ff;
        }

        .bg-success-100 {
            background-color: #dcfce7;
        }

        .text-success-800 {
            color: #166534;
        }

        .bg-danger-100 {
            background-color: #fee2e2;
        }

        .text-danger-800 {
            color: #991b1b;
        }

        .bg-info-100 {
            background-color: #e0f2fe;
        }

        .text-info-800 {
            color: #075985;
        }
    </style>
@endsection