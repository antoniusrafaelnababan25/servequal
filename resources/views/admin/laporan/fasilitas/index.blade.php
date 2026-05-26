@extends('layouts.admin')

@section('title', 'Laporan Penilaian Fasilitas - Admin')
@section('page_title', 'Laporan Penilaian Fasilitas')

@section('content')
    <div class="container-fluid px-0">
        <!-- Filter Periode -->
        <div class="bg-white rounded-4 shadow-sm p-3 mb-4">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-0">
                        <i class="bi bi-calendar-week me-2 text-success"></i>Periode Kuesioner
                    </label>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('admin.laporan.fasilitas.index') }}" id="periodeForm">
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
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">Menampilkan:
                            {{ $periodeTerpilih->nama_periode }}</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">Menampilkan semua periode</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6">
                <div class="bg-white rounded-4 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0 small">Total Penilaian</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($statistik['total_penilaian']) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3"><i
                                class="bi bi-file-text text-success fs-5"></i></div>
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
                        <div class="bg-success bg-opacity-10 rounded-circle p-3"><i
                                class="bi bi-star-fill text-success fs-5"></i></div>
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
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3"><i
                                class="bi bi-trophy text-warning fs-5"></i></div>
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
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3"><i
                                class="bi bi-arrow-down text-danger fs-5"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <form method="GET" action="{{ route('admin.laporan.fasilitas.index') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Mahasiswa</label>
                        <select name="mahasiswa_id" class="form-select bg-light border-0 rounded-3">
                            <option value="">Semua</option>
                            @foreach($mahasiswaList as $m)
                                <option value="{{ $m->id }}" {{ request('mahasiswa_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Jurusan</label>
                        <select name="jurusan_id" class="form-select bg-light border-0 rounded-3">
                            <option value="">Semua</option>
                            @foreach($jurusanList as $j)
                                <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Min Rating</label>
                        <input type="number" step="0.1" min="0" max="5" name="min_rating"
                            class="form-control bg-light border-0 rounded-3" value="{{ request('min_rating') }}"
                            placeholder="Min">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Max Rating</label>
                        <input type="number" step="0.1" min="0" max="5" name="max_rating"
                            class="form-control bg-light border-0 rounded-3" value="{{ request('max_rating') }}"
                            placeholder="Max">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success rounded-pill w-100"><i
                                class="bi bi-funnel me-1"></i>Filter</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control bg-light border-0 rounded-3"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control bg-light border-0 rounded-3"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-6 text-end">
                        @if(request()->anyFilled(['mahasiswa_id', 'jurusan_id', 'start_date', 'end_date', 'min_rating', 'max_rating']))
                            <a href="{{ route('admin.laporan.fasilitas.index') }}"
                                class="btn btn-sm btn-outline-secondary rounded-pill"><i
                                    class="bi bi-x-circle me-1"></i>Reset</a>
                        @endif
                        <a href="{{ route('admin.laporan.fasilitas.export-excel', request()->query()) }}"
                            class="btn btn-success rounded-pill ms-2"><i class="bi bi-file-earmark-excel me-1"></i>Export
                            Excel</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Chart -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <h5 class="fw-semibold mb-3"><i class="bi bi-bar-chart-steps me-2 text-success"></i>Grafik Gap - Penilaian
                Fasilitas</h5>
            <canvas id="fasilitasChart" height="300" style="width: 100%; max-height: 400px;"></canvas>
        </div>

        <!-- Tabel -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0"><i class="bi bi-table me-2 text-success"></i>Data Penilaian Fasilitas</h5>
                <span class="text-muted small">Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari
                    {{ $data->total() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>NIM</th>
                            <th>Rata-rata</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                            <tr>
                                <td class="text-center">{{ $data->firstItem() + $index }}</td>
                                <td class="fw-medium">{{ $item->mahasiswa_nama }}</td>
                                <td>{{ $item->mahasiswa_nim }}</td>
                                <td><span
                                        class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">{{ number_format($item->rata_rata, 2) }}</span>
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.laporan.fasilitas.detail-mahasiswa', $item->mahasiswa_id) }}"
                                        class="btn btn-sm btn-outline-info rounded-pill me-1">Detail</a>
                                    <button class="btn btn-sm btn-outline-success rounded-pill view-detail"
                                        data-id="{{ $item->id }}" data-mahasiswa="{{ $item->mahasiswa_nama }}"
                                        data-nim="{{ $item->mahasiswa_nim }}"
                                        data-url="{{ route('admin.laporan.fasilitas.detail-jawaban', $item->id) }}">
                                        <i class="bi bi-eye me-1"></i>Jawaban
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">Belum ada data penilaian</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $data->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>

    <!-- Modal Detail Jawaban -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-success bg-opacity-10">
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-chat-dots me-2 text-success"></i>Detail Jawaban - Penilaian Fasilitas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted">Mahasiswa</small>
                                <div class="fw-semibold" id="modalMahasiswa">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted">NIM</small>
                                <div class="fw-semibold" id="modalNim">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="55%">Pertanyaan</th>
                                    <th width="10%">Kategori</th>
                                    <th width="10%">Harapan</th>
                                    <th width="10%">Persepsi</th>
                                    <th width="10%">Gap</th>
                                </tr>
                            </thead>
                            <tbody id="modalJawaban">
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Load Chart
        function loadChart() {
            let params = new URLSearchParams(window.location.search);
            axios.get('{{ route("admin.laporan.fasilitas.chart-data") }}?' + params)
                .then(res => {
                    let data = res.data;
                    let labels = Object.keys(data).map(k => data[k].label);
                    let persepsi = Object.keys(data).map(k => data[k].persepsi);
                    let harapan = Object.keys(data).map(k => data[k].harapan);

                    new Chart(document.getElementById('fasilitasChart'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                { label: 'Persepsi (Kinerja)', data: persepsi, backgroundColor: '#28a745', borderRadius: 8 },
                                { label: 'Harapan', data: harapan, backgroundColor: '#20c997', borderRadius: 8 }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: { y: { beginAtZero: true, max: 5, title: { display: true, text: 'Skor (1-5)' } } }
                        }
                    });
                })
                .catch(err => console.error('Error loading chart:', err));
        }

        // Load Detail Jawaban via AJAX
        function loadDetailJawaban(penilaianId, mahasiswa, nim) {
            document.getElementById('modalMahasiswa').innerText = mahasiswa;
            document.getElementById('modalNim').innerText = nim;
            document.getElementById('modalJawaban').innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-success" role="status"></div></td></tr>';

            axios.get('/admin/laporan-fasilitas/jawaban/' + penilaianId)
                .then(res => {
                    if (res.data.success) {
                        let jawaban = res.data.data.jawaban;
                        let tbody = document.getElementById('modalJawaban');
                        tbody.innerHTML = '';

                        if (jawaban.length > 0) {
                            jawaban.forEach((item, idx) => {
                                let gapClass = item.gap >= 0 ? 'text-success' : 'text-danger';
                                tbody.innerHTML += `
                                <tr>
                                    <td class="text-center">${idx + 1}</td>
                                    <td>${item.pertanyaan}</td>
                                    <td class="text-center">${item.kategori}</td>
                                    <td class="text-center">${item.harapan}</td>
                                    <td class="text-center">${item.persepsi}</td>
                                    <td class="text-center ${gapClass} fw-bold">${item.gap >= 0 ? '+' : ''}${item.gap}</td>
                                </tr>
                            `;
                            });
                        } else {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data jawaban</td></tr>';
                        }
                    } else {
                        document.getElementById('modalJawaban').innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data: ' + (res.data.message || 'Unknown error') + '</td></tr>';
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    document.getElementById('modalJawaban').innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Terjadi kesalahan saat memuat data</td></tr>';
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadChart();

            // Event listener untuk tombol detail jawaban
            document.querySelectorAll('.view-detail').forEach(btn => {
                btn.addEventListener('click', function () {
                    let id = this.dataset.id;
                    let mahasiswa = this.dataset.mahasiswa;
                    let nim = this.dataset.nim;
                    loadDetailJawaban(id, mahasiswa, nim);
                    new bootstrap.Modal(document.getElementById('detailModal')).show();
                });
            });

            // Auto submit filter on change
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