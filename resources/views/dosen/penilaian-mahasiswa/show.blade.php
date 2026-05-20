@extends('layouts.dosen')

@section('title', 'Detail Penilaian Mahasiswa - Dosen')
@section('page_title', 'Detail Penilaian Mahasiswa')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="mb-3">
                    <a href="{{ route('dosen.penilaian-mahasiswa.index') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <!-- Info Penilaian -->
                <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-purple-100 rounded-circle p-3">
                            <i class="bi bi-person fs-3 text-purple-800"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">Detail Penilaian</h4>
                            <p class="text-muted mb-0">Informasi lengkap penilaian dari mahasiswa</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted">Mahasiswa</small>
                                <h6 class="mb-0">{{ $penilaian->mahasiswa->name ?? $penilaian->mahasiswa_nama ?? '-' }}</h6>
                                <small>NIM: {{ $penilaian->mahasiswa->nim ?? $penilaian->mahasiswa_nim ?? '-' }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted">Info Penilaian</small>
                                <h6 class="mb-0">{{ $penilaian->kelas ?? '-' }}</h6>
                                <small>Mata Kuliah: {{ $penilaian->mata_kuliah ?? '-' }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted">Periode</small>
                                <h6 class="mb-0">{{ $penilaian->periode->nama_periode ?? '-' }}</h6>
                                <small>Tanggal: {{ $penilaian->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted">Rata-rata</small>
                                <h6 class="mb-0 text-purple-600 fw-bold">{{ number_format($penilaian->rata_rata, 2) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Jawaban -->
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-file-text me-2 text-purple-600"></i>Detail Jawaban</h5>

                    @php
                        $dimensiOrder = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
                    @endphp

                    @foreach($dimensiOrder as $dimensi)
                        @if(isset($dimensiJawaban[$dimensi]) && count($dimensiJawaban[$dimensi]) > 0)
                            <div class="card border-0 rounded-3 mb-3 shadow-sm">
                                <div class="card-header bg-purple-100 border-0 py-3">
                                    <h6 class="fw-semibold text-purple-800 mb-0">{{ $dimensi }}</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50">No</th>
                                                    <th>Pertanyaan</th>
                                                    <th width="120">Harapan</th>
                                                    <th width="120">Persepsi</th>
                                                    <th width="100">Gap</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dimensiJawaban[$dimensi] as $item)
                                                    <tr>
                                                        <td class="text-center">{{ $item['no'] }}</td>
                                                        <td>{{ $item['pertanyaan'] }}</td>
                                                        <td class="text-center">{{ $item['harapan'] }}</td>
                                                        <td class="text-center">{{ $item['persepsi'] }}</td>
                                                        <td
                                                            class="text-center {{ $item['gap'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                                            {{ $item['gap'] >= 0 ? '+' : '' }}{{ $item['gap'] }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection