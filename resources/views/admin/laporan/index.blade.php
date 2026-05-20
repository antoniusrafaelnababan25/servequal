@extends('layouts.admin')

@section('title', 'Laporan Penilaian Dosen - Admin')
@section('page_title', 'Laporan Penilaian Dosen (SERVQUAL)')

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
                    <form method="GET" action="{{ route('admin.laporan.index') }}" id="periodeForm">
                        <select name="periode_id" class="form-select bg-light border-0 rounded-3"
                            onchange="this.form.submit()">
                            <option value="">-- Semua Periode --</option>
                            @foreach($periodeList as $periode)
                                <option value="{{ $periode->id }}" {{ $periodeTerpilih && $periodeTerpilih->id == $periode->id ? 'selected' : '' }}>
                                    {{ $periode->nama_periode }}
                                    ({{ $periode->tanggal_mulai->format('d/m/Y') }} -
                                    {{ $periode->tanggal_selesai->format('d/m/Y') }})
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

        <!-- Statistik Ringkasan -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Total Penilaian</p>
                            <h3 class="mb-0 fw-bold text-purple-800">{{ number_format($statistik['total_penilaian']) }}</h3>
                        </div>
                        <div class="bg-purple-100 rounded-circle p-3">
                            <i class="bi bi-file-text text-purple-600 fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Rata-rata Kepuasan</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($statistik['rata_rata'], 2) }}</h3>
                        </div>
                        <div class="bg-green-100 rounded-circle p-3">
                            <i class="bi bi-star-fill text-success fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Nilai Tertinggi</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ number_format($statistik['tertinggi'], 2) }}</h3>
                        </div>
                        <div class="bg-yellow-100 rounded-circle p-3">
                            <i class="bi bi-trophy text-warning fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Nilai Terendah</p>
                            <h3 class="mb-0 fw-bold text-danger">{{ number_format($statistik['terendah'], 2) }}</h3>
                        </div>
                        <div class="bg-red-100 rounded-circle p-3">
                            <i class="bi bi-arrow-down text-danger fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <form method="GET" action="{{ route('admin.laporan.index') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="bi bi-person-badge me-1"></i>Dosen</label>
                        <select name="dosen_id" class="form-select bg-light border-0 rounded-3">
                            <option value="">Semua Dosen</option>
                            @foreach($dosenList as $dosen)
                                <option value="{{ $dosen->id }}" {{ request('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                    {{ $dosen->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="bi bi-building me-1"></i>Jurusan</label>
                        <select name="jurusan_id" class="form-select bg-light border-0 rounded-3">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusanList as $jurusan)
                                <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><i class="bi bi-calendar me-1"></i>Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control bg-light border-0 rounded-3"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><i class="bi bi-calendar me-1"></i>Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control bg-light border-0 rounded-3"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-purple rounded-pill w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-end">
                        @if(request()->anyFilled(['dosen_id', 'jurusan_id', 'start_date', 'end_date']))
                            <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="bi bi-x-circle me-1"></i>Reset Filter
                            </a>
                        @endif
                        <a href="{{ route('admin.laporan.export-excel', request()->query()) }}"
                            class="btn btn-success rounded-pill ms-2">
                            <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Chart Gap SERVQUAL -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-semibold mb-0"><i class="bi bi-bar-chart-steps me-2 text-purple-600"></i>Grafik Gap SERVQUAL
                    (Persepsi vs Harapan)</h5>
                <span class="badge bg-purple-100 text-purple-800 px-3 py-2 rounded-pill">Metode SERVQUAL</span>
            </div>
            <canvas id="laporanChart" height="300" style="width: 100%; max-height: 400px;"></canvas>
            <div class="text-muted small mt-3 text-center">
                <i class="bi bi-info-circle"></i> Gap negatif menunjukkan layanan di bawah harapan
            </div>
        </div>

        <!-- Tabel Data Penilaian Dosen -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-semibold mb-0"><i class="bi bi-table me-2 text-purple-600"></i>Data Penilaian Dosen</h5>
                <span class="text-muted small">Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari
                    {{ $data->total() }} data</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Dosen</th>
                            <th>Mahasiswa</th>
                            <th>NIM</th>
                            <th>Kelas</th>
                            <th>Mata Kuliah</th>
                            <th>Rata-rata</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                            <tr>
                                <td>{{ $data->firstItem() + $index }}</td>
                                <td class="fw-medium">{{ $item->dosen_nama }}</td>
                                <td>{{ $item->mahasiswa_nama }}</td>
                                <td>{{ $item->mahasiswa_nim }}</td>
                                <td>{{ $item->kelas ?? '-' }}</td>
                                <td>{{ $item->mata_kuliah ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-purple-100 text-purple-800 rounded-pill px-3 py-1">
                                        {{ number_format($item->rata_rata, 2) }}
                                    </span>
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.laporan.detail-dosen', $item->dosen_id) }}"
                                            class="btn btn-outline-info rounded-pill me-1" title="Detail Dosen">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-purple rounded-pill view-detail"
                                            data-id="{{ $item->id }}" data-mahasiswa="{{ $item->mahasiswa_nama }}"
                                            data-nim="{{ $item->mahasiswa_nim }}" data-kelas="{{ $item->kelas }}"
                                            data-mata_kuliah="{{ $item->mata_kuliah }}" data-nilai='@json($item->nilai)'
                                            title="Lihat Jawaban">
                                            <i class="bi bi-chat-dots"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
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

    <!-- Modal Detail Jawaban -->
    <div class="modal fade" id="detailJawabanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0 bg-purple-100">
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-chat-dots me-2 text-purple-600"></i>Detail Jawaban Mahasiswa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small text-uppercase">Mahasiswa</div>
                                <div class="fw-semibold fs-6" id="detailMahasiswa">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small text-uppercase">NIM</div>
                                <div class="fw-semibold" id="detailNim">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small text-uppercase">Kelas</div>
                                <div class="fw-semibold" id="detailKelas">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <div class="text-muted small text-uppercase">Mata Kuliah</div>
                                <div class="fw-semibold" id="detailMataKuliah">-</div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-semibold mb-3"><i class="bi bi-table me-2"></i>Detail Jawaban per Pertanyaan</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="50%">Pertanyaan</th>
                                    <th width="15%">Harapan</th>
                                    <th width="15%">Persepsi</th>
                                    <th width="15%">Gap</th>
                                </tr>
                            </thead>
                            <tbody id="detailJawabanBody">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chartInstance = null;

        function loadChart() {
            let params = new URLSearchParams(window.location.search);
            let url = '{{ route("admin.laporan.chart-data") }}?' + params.toString();

            axios.get(url)
                .then(response => {
                    let chartData = response.data;
                    let labels = Object.keys(chartData);
                    let persepsi = labels.map(d => chartData[d].persepsi);
                    let harapan = labels.map(d => chartData[d].harapan);

                    if (chartInstance) {
                        chartInstance.destroy();
                    }

                    let ctx = document.getElementById('laporanChart').getContext('2d');
                    chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                { label: 'Persepsi (Kinerja)', data: persepsi, backgroundColor: '#8b5cf6', borderRadius: 8, barPercentage: 0.65, categoryPercentage: 0.8 },
                                { label: 'Harapan', data: harapan, backgroundColor: '#c084fc', borderRadius: 8, barPercentage: 0.65, categoryPercentage: 0.8 }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            let label = context.dataset.label || '';
                                            let value = context.raw;
                                            let index = context.dataIndex;
                                            if (context.dataset.label === 'Persepsi (Kinerja)') {
                                                let gap = (value - harapan[index]).toFixed(2);
                                                return label + ': ' + value + ' (Gap: ' + gap + ')';
                                            }
                                            return label + ': ' + value;
                                        }
                                    }
                                },
                                legend: { position: 'top' }
                            },
                            scales: {
                                y: { beginAtZero: true, max: 5, title: { display: true, text: 'Skor (1-5)' }, ticks: { stepSize: 1 } }
                            }
                        }
                    });
                })
                .catch(err => console.error('Error loading chart:', err));
        }

        function formatJawaban(nilai) {
            let result = [];
            if (!nilai) return result;

            let data = nilai;
            if (typeof nilai === 'string') {
                try { data = JSON.parse(nilai); } catch (e) { return result; }
            }

            if (typeof data === 'object' && data !== null) {
                const keys = Object.keys(data).sort((a, b) => parseInt(a) - parseInt(b));
                for (let i = 0; i < keys.length; i++) {
                    const key = keys[i];
                    const item = data[key];
                    if (typeof item === 'object') {
                        result.push({
                            id_pertanyaan: item.id_pertanyaan || key,
                            harapan: parseInt(item.harapan) || 0,
                            persepsi: parseInt(item.persepsi) || 0,
                            teks: item.teks || 'Pertanyaan ' + (item.id_pertanyaan || key)
                        });
                    }
                }
            }
            return result;
        }

        function displayJawaban(jawabanArray) {
            const tbody = document.getElementById('detailJawabanBody');
            tbody.innerHTML = '';

            if (jawabanArray && jawabanArray.length > 0) {
                jawabanArray.forEach((item, index) => {
                    const harapan = item.harapan || 0;
                    const persepsi = item.persepsi || 0;
                    const gap = (persepsi - harapan).toFixed(2);
                    const gapClass = gap >= 0 ? 'text-success' : 'text-danger';
                    const pertanyaan = item.teks || 'Pertanyaan ' + (item.id_pertanyaan || index + 1);
                    tbody.innerHTML += `<tr><td class="text-center">${index + 1}</td><td>${pertanyaan}</td><td class="text-center">${harapan}</td><td class="text-center">${persepsi}</td><td class="text-center ${gapClass} fw-bold">${gap >= 0 ? '+' : ''}${gap}</td></tr>`;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data jawaban</td></tr>';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadChart();

            document.getElementById('filterForm')?.addEventListener('submit', function (e) {
                e.preventDefault();
                let url = new URL(window.location.href);
                let params = new URLSearchParams(new FormData(this));
                url.search = params.toString();
                window.location.href = url.toString();
            });

            document.getElementById('periodeForm')?.addEventListener('submit', function (e) {
                e.preventDefault();
                let url = new URL(window.location.href);
                let params = new URLSearchParams(new FormData(this));
                url.search = params.toString();
                window.location.href = url.toString();
            });

            document.querySelectorAll('.view-detail').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('detailMahasiswa').innerText = this.dataset.mahasiswa;
                    document.getElementById('detailNim').innerText = this.dataset.nim;
                    document.getElementById('detailKelas').innerText = this.dataset.kelas || '-';
                    document.getElementById('detailMataKuliah').innerText = this.dataset.mata_kuliah || '-';
                    displayJawaban(formatJawaban(this.dataset.nilai));
                    new bootstrap.Modal(document.getElementById('detailJawabanModal')).show();
                });
            });
        });
    </script>
@endpush