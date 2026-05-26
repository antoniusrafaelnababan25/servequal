@extends('layouts.admin')

@section('title', 'Laporan Penilaian Dosen - Admin')
@section('page_title', 'Laporan Penilaian Dosen')

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
                                    ({{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/Y') }})
                                    @if($periode->is_active) - Aktif @endif
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    @if($periodeTerpilih)
                        <span class="badge bg-purple-100 text-purple-800 px-3 py-2">Menampilkan:
                            {{ $periodeTerpilih->nama_periode }}</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">Menampilkan semua periode</span>
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
                        <div class="bg-purple-100 rounded-circle p-3"><i class="bi bi-file-text text-purple-600 fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Rata-rata</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($statistik['rata_rata'], 2) }}</h3>
                        </div>
                        <div class="bg-green-100 rounded-circle p-3"><i class="bi bi-star-fill text-success fs-5"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Tertinggi</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ number_format($statistik['tertinggi'], 2) }}</h3>
                        </div>
                        <div class="bg-yellow-100 rounded-circle p-3"><i class="bi bi-trophy text-warning fs-5"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Terendah</p>
                            <h3 class="mb-0 fw-bold text-danger">{{ number_format($statistik['terendah'], 2) }}</h3>
                        </div>
                        <div class="bg-red-100 rounded-circle p-3"><i class="bi bi-arrow-down text-danger fs-5"></i></div>
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
                        <label class="form-label fw-semibold"><i class="bi bi-star me-1"></i>Min Rating</label>
                        <input type="number" step="0.1" min="0" max="5" name="min_rating"
                            class="form-control bg-light border-0 rounded-3" value="{{ request('min_rating') }}"
                            placeholder="Min">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><i class="bi bi-star me-1"></i>Max Rating</label>
                        <input type="number" step="0.1" min="0" max="5" name="max_rating"
                            class="form-control bg-light border-0 rounded-3" value="{{ request('max_rating') }}"
                            placeholder="Max">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-purple rounded-pill w-100"><i
                                class="bi bi-funnel me-1"></i>Filter</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="bi bi-calendar me-1"></i>Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control bg-light border-0 rounded-3"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="bi bi-calendar me-1"></i>Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control bg-light border-0 rounded-3"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-6 text-end">
                        @if(request()->anyFilled(['dosen_id', 'jurusan_id', 'start_date', 'end_date', 'min_rating', 'max_rating']))
                            <a href="{{ route('admin.laporan.index') }}"
                                class="btn btn-sm btn-outline-secondary rounded-pill"><i
                                    class="bi bi-x-circle me-1"></i>Reset</a>
                        @endif
                        <a href="{{ route('admin.laporan.export-excel', request()->query()) }}"
                            class="btn btn-success rounded-pill ms-2"><i class="bi bi-file-earmark-excel me-1"></i>Export
                            Excel</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Chart Gap SERVQUAL -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <h5 class="fw-semibold mb-3"><i class="bi bi-bar-chart-steps me-2 text-purple-600"></i>Grafik Gap SERVQUAL
                (Persepsi vs Harapan)</h5>
            <canvas id="laporanChart" height="300" style="width: 100%; max-height: 400px;"></canvas>
        </div>

        <!-- Tabel Data Penilaian Dosen -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0"><i class="bi bi-table me-2 text-purple-600"></i>Data Penilaian Dosen</h5>
                <span class="text-muted small">Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari
                    {{ $data->total() }}</span>
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
                                <td class="text-center">{{ $data->firstItem() + $index }}</td>
                                <td class="fw-medium">{{ $item->dosen_nama }}</td>
                                <td>{{ $item->mahasiswa_nama }}</td>
                                <td>{{ $item->mahasiswa_nim }}</td>
                                <td>{{ $item->kelas ?? '-' }}</td>
                                <td>{{ $item->mata_kuliah ?? '-' }}</td>
                                <td><span
                                        class="badge bg-purple-100 text-purple-800 rounded-pill px-3 py-1">{{ number_format($item->rata_rata, 2) }}</span>
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.laporan.detail-dosen', $item->dosen_id) }}"
                                        class="btn btn-sm btn-outline-info rounded-pill">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">Belum ada data penilaian</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $data->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chartInstance = null;

        function loadChart() {
            let params = new URLSearchParams(window.location.search);
            axios.get('{{ route("admin.laporan.chart-data") }}?' + params)
                .then(res => {
                    let data = res.data;
                    let labels = Object.keys(data);
                    let persepsi = labels.map(k => data[k].persepsi);
                    let harapan = labels.map(k => data[k].harapan);

                    if (chartInstance) chartInstance.destroy();

                    chartInstance = new Chart(document.getElementById('laporanChart'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                { label: 'Persepsi (Kinerja)', data: persepsi, backgroundColor: '#8b5cf6', borderRadius: 8 },
                                { label: 'Harapan', data: harapan, backgroundColor: '#c084fc', borderRadius: 8 }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 5,
                                    title: { display: true, text: 'Skor (1-5)' },
                                    ticks: { stepSize: 1 }
                                }
                            }
                        }
                    });
                })
                .catch(err => console.error('Error loading chart:', err));
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadChart();

            document.getElementById('periodeForm')?.addEventListener('submit', function (e) {
                e.preventDefault();
                let url = new URL(window.location.href);
                let params = new URLSearchParams(new FormData(this));
                url.search = params.toString();
                window.location.href = url.toString();
            });
        });
    </script>
@endpush