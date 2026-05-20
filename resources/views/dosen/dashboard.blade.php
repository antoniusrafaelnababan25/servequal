@extends('layouts.dosen')

@section('title', 'Dashboard Dosen - SERVQUAL POLMED')
@section('page_title', 'Dashboard')

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
                    <form method="GET" action="{{ route('dosen.dashboard') }}" id="periodeForm">
                        <select name="periode_id" class="form-select bg-light border-0 rounded-3"
                            onchange="this.form.submit()">
                            <option value="">-- Semua Periode --</option>
                            @foreach($periodeList as $periode)
                                <option value="{{ $periode->id }}" {{ $periodeTerpilih && $periodeTerpilih->id == $periode->id ? 'selected' : '' }}>
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
                    @if($periodeTerpilih)
                        <span class="badge bg-purple-100 text-purple-800 px-3 py-2">
                            <i class="bi bi-info-circle me-1"></i>Menampilkan: {{ $periodeTerpilih->nama_periode }}
                        </span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                            <i class="bi bi-info-circle me-1"></i>Menampilkan semua periode
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-4 shadow-lg p-4 mb-4 text-white"
            style="background: linear-gradient(135deg, #4c1d95 0%, #2e1065 100%);">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
                    <p class="mb-0 opacity-75">Berikut adalah ringkasan penilaian kinerja Anda dari mahasiswa.</p>
                </div>
                <div class="text-center">
                    <div class="bg-white/20 rounded-circle p-3">
                        <i class="bi bi-emoji-smile fs-1"></i>
                    </div>
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
                            <h3 class="mb-0 fw-bold text-purple-600 display-6">{{ number_format($totalPenilaian) }}</h3>
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
                                {{ number_format($rataRataKeseluruhan, 2) }}<span class="fs-5 text-muted">/5</span>
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
                            <h3 class="mb-0 fw-bold text-success display-6">{{ number_format($nilaiTertinggi, 2) }}</h3>
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
                            <h3 class="mb-0 fw-bold text-danger display-6">{{ number_format($nilaiTerendah, 2) }}</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-arrow-down-short text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Kuesioner Fasilitas -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                class="rounded-circle p-3 {{ $sudahIsiFasilitas ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10' }}">
                                <i
                                    class="bi {{ $sudahIsiFasilitas ? 'bi-check-lg text-success' : 'bi-question-lg text-warning' }} fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-1">Kuesioner Fasilitas</h5>
                                <p class="text-muted small mb-0">Penilaian terhadap fasilitas kampus</p>
                            </div>
                        </div>
                        <div>
                            @if($sudahIsiFasilitas)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-4 py-2">
                                    <i class="bi bi-check-circle-fill me-1"></i>Sudah diisi
                                </span>
                            @else
                                <a href="{{ route('dosen.kuesioner-fasilitas.create') }}"
                                    class="btn btn-purple rounded-pill px-4">
                                    <i class="bi bi-pencil-square me-2"></i>Isi Kuesioner
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart SERVQUAL Gap -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-semibold mb-0">
                            <i class="bi bi-graph-up me-2 text-purple-600"></i>Analisis Gap SERVQUAL (Persepsi vs Harapan)
                        </h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="toggleGapChart"
                                style="background-color: #4c1d95;">
                            <label class="form-check-label" for="toggleGapChart">Tampilkan Gap</label>
                        </div>
                    </div>
                    <div class="position-relative">
                        <canvas id="gapChart" height="120" style="max-height: 400px; width: 100%;"></canvas>
                    </div>
                    <div class="text-muted small mt-3 text-center">
                        <i class="bi bi-info-circle"></i> *Gap negatif menunjukkan layanan di bawah harapan mahasiswa
                    </div>
                </div>
            </div>
        </div>

        <!-- Penilaian Terbaru -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-clock-history me-2 text-purple-600"></i>Penilaian Terbaru dari Mahasiswa
                </h5>
                <a href="{{ route('dosen.penilaian-mahasiswa.index') }}" class="btn btn-sm btn-outline-purple rounded-pill">
                    <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                </a>
            </div>

            @if($penilaianTerbaru->isNotEmpty())
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
                            @foreach($penilaianTerbaru as $penilaian)
                                <tr>
                                    <td>{{ $penilaian->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($penilaian->periode)
                                            <span class="badge bg-purple-100 text-purple-800 rounded-pill px-2 py-1"
                                                style="font-size: 0.7rem;">
                                                {{ $penilaian->periode->nama_periode }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="fw-medium">{{ $penilaian->mahasiswa->name ?? '-' }}</td>
                                    <td>{{ $penilaian->mahasiswa->nim ?? '-' }}</td>
                                    <td>{{ $penilaian->kelas ?? '-' }}</td>
                                    <td>{{ $penilaian->mata_kuliah ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $penilaian->rata_rata >= 4 ? 'success' : ($penilaian->rata_rata >= 3 ? 'warning' : 'danger') }} rounded-pill px-3 py-2">
                                            {{ number_format($penilaian->rata_rata, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-purple rounded-pill view-detail"
                                            data-id="{{ $penilaian->id }}" title="Lihat Detail Jawaban">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p>Belum ada penilaian dari mahasiswa</p>
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

@push('scripts')
    <script>
        let gapChart = null;
        let chartData = @json($chartJsData);
        let showGap = false;

        function updateChart() {
            const ctx = document.getElementById('gapChart').getContext('2d');

            if (gapChart) {
                gapChart.destroy();
            }

            let datasets = [];

            if (showGap) {
                datasets.push({
                    label: 'Gap (Persepsi - Harapan)',
                    data: chartData.gap,
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1,
                    borderRadius: 8,
                    type: 'bar'
                });
            } else {
                datasets.push({
                    label: 'Persepsi (Kinerja)',
                    data: chartData.persepsi,
                    backgroundColor: 'rgba(76, 29, 149, 0.7)',
                    borderColor: 'rgb(76, 29, 149)',
                    borderWidth: 1,
                    borderRadius: 8
                });
                datasets.push({
                    label: 'Harapan',
                    data: chartData.harapan,
                    backgroundColor: 'rgba(245, 158, 11, 0.7)',
                    borderColor: 'rgb(245, 158, 11)',
                    borderWidth: 1,
                    borderRadius: 8
                });
            }

            gapChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    let value = context.raw;
                                    return `${label}: ${value.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            title: { display: true, text: 'Skor (1-5)', font: { weight: 'bold' } },
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            title: { display: true, text: 'Dimensi SERVQUAL', font: { weight: 'bold' } }
                        }
                    }
                }
            });
        }

        // Toggle Gap Chart
        document.getElementById('toggleGapChart').addEventListener('change', function (e) {
            showGap = e.target.checked;
            updateChart();
        });

        // Initialize chart
        updateChart();

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

                fetch('/dosen/detail-jawaban/' + id)
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
                                                <h6 class="mb-0">${d.mahasiswa}</h6>
                                                <small>NIM: ${d.nim}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="bg-white rounded-3 p-3">
                                                <small class="text-muted text-uppercase">Info Penilaian</small>
                                                <h6 class="mb-0">${d.kelas || '-'}</h6>
                                                <small>Mata Kuliah: ${d.mata_kuliah || '-'}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="bg-white rounded-3 p-3">
                                                <small class="text-muted text-uppercase">Periode</small>
                                                <h6 class="mb-0">${d.periode || '-'}</h6>
                                                <small>Tanggal: ${d.tanggal}</small>
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
                            if (d.dimensi_jawaban) {
                                const dimensiOrder = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
                                for (const dimensi of dimensiOrder) {
                                    if (d.dimensi_jawaban[dimensi] && d.dimensi_jawaban[dimensi].length > 0) {
                                        const jawaban = d.dimensi_jawaban[dimensi];
                                        html += `
                                            <div class="card border-0 rounded-3 mb-3">
                                                <div class="card-header bg-purple-100 border-0 rounded-top-3">
                                                    <h6 class="fw-semibold text-purple-800 mb-0">${dimensi}</h6>
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
                                                    <td>${item.pertanyaan}</td>
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
                            } else if (d.jawaban && d.jawaban.length > 0) {
                                // Jika tidak ter-group, tampilkan semua
                                html += `
                                    <div class="card border-0 rounded-3 mb-3">
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
                                d.jawaban.forEach(item => {
                                    const gapColor = item.gap >= 0 ? 'text-success' : 'text-danger';
                                    html += `
                                        <tr>
                                            <td class="text-center">${item.no}</td>
                                            <td>${item.pertanyaan}</td>
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
                            } else {
                                html += `<div class="alert alert-warning">Tidak ada data jawaban</div>`;
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
    </script>
@endpush