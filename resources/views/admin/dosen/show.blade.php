@extends('layouts.admin')

@section('title', 'Detail Dosen - Admin')
@section('page_title', 'Detail Dosen')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="mx-auto bg-purple-100 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
                            <i class="bi bi-person-circle fs-1 text-purple-600"></i>
                        </div>
                        <h4 class="mt-3 mb-0">{{ $dosen->name }}</h4>
                        <span class="badge bg-purple-100 text-purple-800 rounded-pill px-3 py-1 mt-2">Dosen</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Username</div>
                                <div class="fw-semibold">{{ $dosen->username ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Email</div>
                                <div class="fw-semibold">{{ $dosen->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">NIDN</div>
                                <div class="fw-semibold">{{ $dosen->nidn ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Program Studi</div>
                                <div class="fw-semibold">{{ $dosen->prodi->nama_prodi ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Jurusan</div>
                                <div class="fw-semibold">{{ $dosen->prodi->jurusan->nama_jurusan ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Status</div>
                                <div>@if($dosen->is_active)<span
                                    class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1"><i
                                class="bi bi-check-circle-fill me-1"></i>Aktif</span>@else<span
                                                class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1"><i
                                            class="bi bi-x-circle-fill me-1"></i>Nonaktif</span>@endif</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Terdaftar Sejak</div>
                                <div class="fw-semibold">
                                    {{ $dosen->created_at ? $dosen->created_at->format('d/m/Y H:i') : '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.dosen.index') }}" class="btn btn-secondary rounded-pill px-4">Kembali</a>
                        <a href="{{ route('admin.dosen.edit', $dosen->id) }}" class="btn btn-purple rounded-pill px-4">Edit
                            Dosen</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection