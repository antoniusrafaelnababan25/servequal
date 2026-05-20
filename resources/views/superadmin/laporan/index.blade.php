@extends('layouts.superadmin')

@section('title', 'Laporan Penilaian Dosen - SUPER ADMIN')
@section('page_title', 'Laporan Penilaian Dosen')

@section('content')
    <div class="container-fluid px-0">
        <!-- Filter Section -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <h5 class="fw-semibold mb-3"><i class="bi bi-funnel me-2"></i>Filter Laporan</h5>
            <form id="filterForm" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Periode</label>
                    <select name="periode_id" id="periode_id" class="form-select rounded-3">
                        <option value="">Semua Periode</option>
                        @foreach($periodeList as $periode)
                            <option value="{{ $periode->id }}">
                                {{ $periode->nama_periode }} ({{ date('d/m/Y', strtotime($periode->tanggal_mulai)) }} -
                                {{ date('d/m/Y', strtotime($periode->tanggal_selesai)) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-purple rounded-pill px-3 flex-grow-1">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        <button type="button" id="resetFilter" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-arrow-repeat me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistik Card -->
        <div class="row g-4 mb-4" id="statistikCards">
            <div class="col-md-3">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Penilaian</p>
                            <h3 class="mb-0" id="totalPenilaian">0</h3>
                        </div>
                        <div class="bg-purple-100 rounded-circle p-3">
                            <i class="bi bi-file-text fs-4 text-purple-800"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Dosen Dinilai</p>
                            <h3 class="mb-0" id="totalDosen">0</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-person-badge fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Mahasiswa Penilai</p>
                            <h3 class="mb-0" id="totalMahasiswa">0</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-people fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Rata-rata Keseluruhan</p>
                            <h3 class="mb-0" id="rataKeseluruhan">0</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-star fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-graph-up me-2"></i>Grafik Perbandingan Persepsi vs Harapan
                    </h5>
                    <canvas id="servqualChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-bar-chart me-2"></i>Rata-rata per Dimensi</h5>
                    <div id="dimensiStats">
                        <div class="text-center text-muted py-4">Pilih filter untuk melihat data</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Data Penilaian -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-semibold mb-0"><i class="bi bi-table me-2"></i>Data Penilaian Dosen</h5>
                <button type="button" id="exportExcel" class="btn btn-success rounded-pill px-3">
                    <i class="bi bi-file-excel me-1"></i>Export Excel
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="laporanTable">
                    <thead class="table-light">
                        <tr>
                            <th>Periode</th>
                            <th>Dosen</th>
                            <th>NIDN</th>
                            <th>Mahasiswa</th>
                            <th>NIM</th>
                            <th>Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td>
                                    @if($item->periode)
                                        <span class="badge bg-purple-100 text-purple-800">{{ $item->periode->nama_periode }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $item->dosen->name ?? '-' }}</td>
                                <td>{{ $item->dosen->nidn ?? '-' }}</td>
                                <td>{{ $item->mahasiswa->name ?? '-' }}</td>
                                <td>{{ $item->mahasiswa->nim ?? '-' }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $item->rata_rata >= 4 ? 'success' : ($item->rata_rata >= 3 ? 'warning' : 'danger') }} rounded-pill px-3 py-2">
                                        {{ number_format($item->rata_rata, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data penilaian
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $data->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let servqualChart = null;

        function loadChartData() {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);

            fetch('/super-admin/laporan/chart-data?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.chart) {
                        updateChart(data.chart);
                        updateDimensiStats(data.data);
                    } else {
                        console.log('No data');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function loadStatistik() {
            const periodeId = document.getElementById('periode_id').value;
            if (!periodeId) {
                // Reset statistik
                document.getElementById('totalPenilaian').innerText = '0';
                document.getElementById('totalDosen').innerText = '0';
                document.getElementById('totalMahasiswa').innerText = '0';
                document.getElementById('rataKeseluruhan').innerText = '0';
                return;
            }

            fetch('/super-admin/laporan/statistik-periode?periode_id=' + periodeId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('totalPenilaian').innerText = data.statistik.total_penilaian;
                        document.getElementById('totalDosen').innerText = data.statistik.total_dosen;
                        document.getElementById('totalMahasiswa').innerText = data.statistik.total_mahasiswa;
                        document.getElementById('rataKeseluruhan').innerText = data.statistik.rata_rata_keseluruhan.toFixed(2);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function updateChart(data) {
            const ctx = document.getElementById('servqualChart').getContext('2d');

            if (servqualChart) {
                servqualChart.destroy();
            }

            servqualChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Persepsi',
                            data: data.persepsi,
                            backgroundColor: 'rgba(76, 29, 149, 0.7)',
                            borderColor: 'rgb(76, 29, 149)',
                            borderWidth: 1,
                            borderRadius: 8
                        },
                        {
                            label: 'Harapan',
                            data: data.harapan,
                            backgroundColor: 'rgba(245, 158, 11, 0.7)',
                            borderColor: 'rgb(245, 158, 11)',
                            borderWidth: 1,
                            borderRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `${context.dataset.label}: ${context.raw.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            title: {
                                display: true,
                                text: 'Skor'
                            }
                        }
                    }
                }
            });
        }

        function updateDimensiStats(data) {
            const dimensiOrder = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
            let html = '<div class="list-group list-group-flush">';

            dimensiOrder.forEach(dim => {
                if (data[dim]) {
                    const persepsi = data[dim].persepsi;
                    const harapan = data[dim].harapan;
                    const gap = data[dim].gap;
                    const gapColor = gap >= 0 ? 'success' : 'danger';
                    const gapIcon = gap >= 0 ? 'arrow-up' : 'arrow-down';

                    html += `
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-purple-800">${dim}</strong>
                                <span class="badge bg-${gapColor} bg-opacity-10 text-${gapColor} rounded-pill">
                                    <i class="bi bi-${gapIcon} me-1"></i>Gap: ${gap}
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-purple-800" role="progressbar" style="width: ${(persepsi / 5) * 100}%" 
                                    aria-valuenow="${persepsi}" aria-valuemin="0" aria-valuemax="5"></div>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span>Persepsi: <strong>${persepsi}</strong></span>
                                <span>Harapan: <strong>${harapan}</strong></span>
                            </div>
                        </div>
                    `;
                }
            });

            html += '</div>';
            document.getElementById('dimensiStats').innerHTML = html;
        }

        // Filter form submit
        document.getElementById('filterForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(formData);
            window.location.href = '/super-admin/laporan?' + params.toString();
        });

        // Reset filter
        document.getElementById('resetFilter').addEventListener('click', function () {
            document.getElementById('periode_id').value = '';
            window.location.href = '/super-admin/laporan';
        });

        // Export Excel
        document.getElementById('exportExcel').addEventListener('click', function () {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);
            window.location.href = '/super-admin/laporan/export-excel?' + params.toString();
        });

        // Load chart and statistik on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadChartData();
            loadStatistik();
        });

        // Reload chart when periode changes
        document.getElementById('periode_id').addEventListener('change', function () {
            loadChartData();
            loadStatistik();
        });
    </script>
@endpush