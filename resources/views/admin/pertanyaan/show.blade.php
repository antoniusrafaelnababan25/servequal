@extends('layouts.admin')

@section('title', 'Detail Pertanyaan - Admin')
@section('page_title', 'Detail Pertanyaan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Dimensi</div>
                                <div class="fw-semibold"><span
                                        class="badge bg-purple-100 text-purple-800">{{ $pertanyaan->dimensi }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Target Role</div>
                                <div class="fw-semibold">
                                    {{ $pertanyaan->target_role == 'mahasiswa' ? 'Mahasiswa' : 'Dosen' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Teks Pertanyaan</div>
                                <div class="fw-semibold">{{ $pertanyaan->teks }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Status</div>
                                <div>@if($pertanyaan->is_active)<span
                                    class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1"><i
                                class="bi bi-check-circle-fill me-1"></i>Aktif</span>@else<span
                                                class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1"><i
                                            class="bi bi-x-circle-fill me-1"></i>Nonaktif</span>@endif</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small">Tanggal Dibuat</div>
                                <div class="fw-semibold">
                                    {{ $pertanyaan->created_at ? $pertanyaan->created_at->format('d/m/Y H:i') : '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.pertanyaan.index') }}"
                            class="btn btn-secondary rounded-pill px-4">Kembali</a>
                        <a href="{{ route('admin.pertanyaan.edit', $pertanyaan->id) }}"
                            class="btn btn-purple rounded-pill px-4">Edit Pertanyaan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection