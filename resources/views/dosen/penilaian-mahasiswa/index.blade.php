@extends('layouts.dosen')

@section('title', 'Penilaian Mahasiswa - Dosen')
@section('page_title', 'Penilaian dari Mahasiswa')

@section('content')
    <div class="container-fluid px-0">

        <!-- Filter Periode -->
        <div class="bg-white rounded-4 shadow-sm p-3 mb-4">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-0">
                        <i class="bi bi-calendar-week me-2 text-purple-600"></i>Periode Kuesioner
                    </label>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('dosen.penilaian-mahasiswa.index') }}" id="periodeForm">
                        <select name="periode_id" class="form-select bg-light border-0 rounded-3"
                            onchange="this.form.submit()">
                            <option value="">-- Semua Periode --</option>
                            @foreach($periodeList as $periode)
                                <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                    {{ $periode->nama_periode }}
                                    ({{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/Y') }})
                                    @if($periode->is_active) - <span class="text-success">Aktif</span> @endif
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    @if(request('periode_id'))
                        @php $periodeTerpilih = $periodeList->firstWhere('id', request('periode_id')); @endphp
                        @if($periodeTerpilih)
                            <span class="badge bg-purple-100 text-purple-800 px-3 py-2">
                                <i class="bi bi-info-circle me-1"></i>Menampilkan: {{ $periodeTerpilih->nama_periode }}
                            </span>
                        @endif
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                            <i class="bi bi-info-circle me-1"></i>Menampilkan semua periode
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-6">
                <div class="card-hover bg-white rounded-4 shadow-sm p-3 h-100 border-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Total Penilaian</p>
                            <h3 class="mb-0 fw-bold text-purple-600 display-6">{{ number_format($statistik['total']) }}</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(76, 29, 149, 0.1);">
                            <i class="bi bi-star-fill text-purple-600 fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-hover bg-white rounded-4 shadow-sm p-3 h-100 border-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Rata-rata Keseluruhan</p>
                            <h3 class="mb-0 fw-bold text-warning display-6">
                                {{ number_format($statistik['rata_rata'], 2) }}<span class="fs-5 text-muted">/5</span>
                            </h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(245, 158, 11, 0.1);">
                            <i class="bi bi-bar-chart-steps text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-hover bg-white rounded-4 shadow-sm p-3 h-100 border-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Nilai Tertinggi</p>
                            <h3 class="mb-0 fw-bold text-success display-6">{{ number_format($statistik['tertinggi'], 2) }}
                            </h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(34, 197, 94, 0.1);">
                            <i class="bi bi-arrow-up-short text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-hover bg-white rounded-4 shadow-sm p-3 h-100 border-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Nilai Terendah</p>
                            <h3 class="mb-0 fw-bold text-danger display-6">{{ number_format($statistik['terendah'], 2) }}
                            </h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-arrow-down-short text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tambahan -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <form method="GET" action="{{ route('dosen.penilaian-mahasiswa.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Kelas</label>
                    <select name="kelas" class="form-select rounded-3">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Cari Mahasiswa</label>
                    <input type="text" name="search" class="form-control rounded-3" placeholder="Nama / NIM"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Nilai Min</label>
                    <select name="min_rating" class="form-select rounded-3">
                        <option value="">Semua</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ request('min_rating') == $i ? 'selected' : '' }}>>= {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Nilai Max</label>
                    <select name="max_rating" class="form-select rounded-3">
                        <option value="">Semua</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ request('max_rating') == $i ? 'selected' : '' }}>
                                <= {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-purple rounded-pill flex-grow-1">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('dosen.penilaian-mahasiswa.index') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-repeat"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Penilaian -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-table me-2 text-purple-600"></i>Daftar Penilaian Mahasiswa
                </h5>
                <button type="button" id="exportExcel" class="btn btn-success rounded-pill px-3">
                    <i class="bi bi-file-excel me-1"></i>Export Excel
                </button>
            </div>

            @if($penilaian->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Periode</th>
                                <th>Mahasiswa</th>
                                <th>NIM</th>
                                <th>Kelas</th>
                                <th>Mata Kuliah</th>
                                <th>Rata-rata</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penilaian as $item)
                                <tr>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($item->periode)
                                            <span class="badge bg-purple-100 text-purple-800 rounded-pill px-2 py-1"
                                                style="font-size: 0.7rem;">
                                                {{ $item->periode->nama_periode }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="fw-medium">{{ $item->mahasiswa->name ?? $item->mahasiswa_nama ?? '-' }}</td>
                                    <td>{{ $item->mahasiswa->nim ?? $item->mahasiswa_nim ?? '-' }}</td>
                                    <td>{{ $item->kelas ?? '-' }}</td>
                                    <td>{{ $item->mata_kuliah ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $item->rata_rata >= 4 ? 'success' : ($item->rata_rata >= 3 ? 'warning' : 'danger') }} rounded-pill px-3 py-2">
                                            {{ number_format($item->rata_rata, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-purple rounded-pill view-detail"
                                            data-id="{{ $item->id }}" title="Lihat Detail Jawaban">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    {{ $penilaian->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p>Belum ada data penilaian dari mahasiswa</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Detail Jawaban -->
    <div class="modal fade" id="detailJawabanModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0 bg-purple-100">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-file-text me-2 text-purple-600"></i>Detail Jawaban Penilaian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailJawabanContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-purple-600" role="status"></div>
                        <p class="mt-2 text-muted">Memuat data...</p>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .btn-outline-purple {
            color: #4c1d95;
            border-color: #4c1d95;
        }

        .btn-outline-purple:hover {
            background-color: #4c1d95;
            color: white;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Export Excel
        document.getElementById('exportExcel').addEventListener('click', function () {
            let params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route("dosen.penilaian-mahasiswa.export-excel") }}?' + params.toString();
        });

        // View detail jawaban
        document.querySelectorAll('.view-detail').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const modal = new bootstrap.Modal(document.getElementById('detailJawabanModal'));
                const contentDiv = document.getElementById('detailJawabanContent');

                contentDiv.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-purple-600" role="status"></div>
                        <p class="mt-2 text-muted">Memuat data...</p>
                    </div>
                `;

                fetch('/dosen/penilaian-mahasiswa/' + id + '/detail')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const d = data.data;
                            let html = `
                                <div class="bg-light rounded-3 p-3 mb-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="bg-white rounded-3 p-3">
                                                <small class="text-muted text-uppercase">Mahasiswa</small>
                                                <h6 class="mb-0">${escapeHtml(d.mahasiswa)}</h6>
                                                <small>NIM: ${escapeHtml(d.nim)}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="bg-white rounded-3 p-3">
                                                <small class="text-muted text-uppercase">Info Penilaian</small>
                                                <h6 class="mb-0">${escapeHtml(d.kelas || '-')}</h6>
                                                <small>Mata Kuliah: ${escapeHtml(d.mata_kuliah || '-')}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="bg-white rounded-3 p-3">
                                                <small class="text-muted text-uppercase">Periode</small>
                                                <h6 class="mb-0">${escapeHtml(d.periode || '-')}</h6>
                                                <small>Tanggal: ${escapeHtml(d.tanggal)}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="bg-white rounded-3 p-3">
                                                <small class="text-muted text-uppercase">Rata-rata</small>
                                                <h6 class="mb-0 text-purple-600 fw-bold">${d.rata_rata}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            // Group by dimensi
                            const dimensiOrder = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
                            for (const dimensi of dimensiOrder) {
                                if (d.dimensi_jawaban && d.dimensi_jawaban[dimensi] && d.dimensi_jawaban[dimensi].length > 0) {
                                    const jawaban = d.dimensi_jawaban[dimensi];
                                    html += `
                                        <div class="card border-0 rounded-3 mb-3">
                                            <div class="card-header bg-purple-100 border-0 rounded-top-3">
                                                <h6 class="fw-semibold text-purple-800 mb-0">${escapeHtml(dimensi)}</h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-sm mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="50">No</th>
                                                                <th>Pertanyaan</th>
                                                                <th width="100" class="text-center">Harapan</th>
                                                                <th width="100" class="text-center">Persepsi</th>
                                                                <th width="80" class="text-center">Gap</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                    `;
                                    jawaban.forEach(item => {
                                        const gapColor = item.gap >= 0 ? 'text-success' : 'text-danger';
                                        html += `
                                            <tr>
                                                <td class="text-center">${item.no}</td>
                                                <td>${escapeHtml(item.pertanyaan)}</td>
                                                <td class="text-center">${item.harapan}</td>
                                                <td class="text-center">${item.persepsi}</td>
                                                <td class="text-center ${gapColor} fw-bold">${item.gap >= 0 ? '+' : ''}${item.gap}</td>
                                            </tr>
                                        `;
                                    });
                                    html += `
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }
                            }

                            contentDiv.innerHTML = html;
                        } else {
                            contentDiv.innerHTML = `<div class="alert alert-danger m-3">${data.message || 'Gagal memuat data'}</div>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        contentDiv.innerHTML = `<div class="alert alert-danger m-3">Terjadi kesalahan saat memuat data</div>`;
                    });

                modal.show();
            });
        });

        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }
    </script>
@endpush