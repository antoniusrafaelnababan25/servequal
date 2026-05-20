@extends('layouts.admin')

@section('title', 'Detail Mahasiswa - Admin')
@section('page_title', 'Detail Mahasiswa')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="mx-auto bg-purple-100 rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;"><i class="bi bi-person-circle fs-1 text-purple-600"></i>
                        </div>
                        <h4 class="mt-3 mb-0">{{ $mahasiswa->name }}</h4>
                        <span class="badge bg-purple-100 text-purple-800 rounded-pill px-3 py-1 mt-2">Mahasiswa</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Username</div>
                                <div class="fw-semibold">{{ $mahasiswa->username ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Email</div>
                                <div class="fw-semibold">{{ $mahasiswa->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">NIM</div>
                                <div class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Program Studi</div>
                                <div class="fw-semibold">{{ $mahasiswa->prodi->nama_prodi ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Jurusan</div>
                                <div class="fw-semibold">{{ $mahasiswa->prodi->jurusan->nama_jurusan ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Kelas</div>
                                <div class="fw-semibold">{{ $mahasiswa->kelas ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Tanggal Lahir</div>
                                <div class="fw-semibold">
                                    {{ $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('d/m/Y') : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Status</div>
                                <div>@if($mahasiswa->is_active)<span
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
                                    {{ $mahasiswa->created_at ? $mahasiswa->created_at->format('d/m/Y H:i') : '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.mahasiswa.index') }}"
                            class="btn btn-secondary rounded-pill px-4">Kembali</a>
                        <a href="{{ route('admin.mahasiswa.edit', $mahasiswa->id) }}"
                            class="btn btn-purple rounded-pill px-4">Edit Mahasiswa</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection