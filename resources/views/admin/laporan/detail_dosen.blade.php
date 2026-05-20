@extends('layouts.admin')

@section('title', 'Detail Penilaian Dosen - Admin')
@section('page_title', 'Detail Penilaian Dosen')

@section('content')
    <div class="container-fluid px-0">
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0"><i class="bi bi-person-badge me-2 text-purple-600"></i>Detail Dosen</h5>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>

            <!-- Info Dosen -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <div class="text-muted small text-uppercase">Nama Dosen</div>
                        <div class="fw-semibold fs-5">{{ $dosen->name }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <div class="text-muted small text-uppercase">NIDN</div>
                        <div class="fw-semibold">{{ $dosen->nidn ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <div class="text-muted small text-uppercase">Jurusan</div>
                        <div class="fw-semibold">{{ $dosen->jurusan ?? $dosen->prodi->jurusan->nama_jurusan ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <div class="text-muted small text-uppercase">Rata-rata Nilai</div>
                        <div class="fw-semibold text-purple-600">
                            {{ number_format($statistik['rata_rata'], 2) ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Ringkasan -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-3 text-center">
                        <div class="text-muted small">Total Penilaian</div>
                        <h4 class="fw-bold text-purple-600 mb-0">{{ number_format($statistik['total']) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-3 text-center">
                        <div class="text-muted small">Nilai Tertinggi</div>
                        <h4 class="fw-bold text-success mb-0">{{ number_format($statistik['tertinggi'], 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-3 text-center">
                        <div class="text-muted small">Nilai Terendah</div>
                        <h4 class="fw-bold text-danger mb-0">{{ number_format($statistik['terendah'], 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Chart SERVQUAL untuk Dosen Ini -->
            <div class="bg-light rounded-3 p-3 mb-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-bar-chart me-2"></i>Grafik Gap SERVQUAL</h6>
                <canvas id="dosenChart" height="250" style="width: 100%;"></canvas>
            </div>

            <!-- Tabel Riwayat Penilaian -->
            <h5 class="fw-semibold mb-3"><i class="bi bi-table me-2 text-purple-600"></i>Riwayat Penilaian Mahasiswa</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Periode</th>
                            <th>Mahasiswa</th>
                            <th>NIM</th>
                            <th>Kelas</th>
                            <th>Mata Kuliah</th>
                            <th>Rata-rata</th>
                            <th>Tanggal Penilaian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penilaian as $index => $p)
                            <tr>
                                <td class="text-center">{{ $penilaian->firstItem() + $index }}</td>
                                <td>
                                    @php
                                        $periodeInfo = \App\Models\KuesionerPeriode::find($p->periode_id);
                                    @endphp
                                    @if($periodeInfo)
                                        <span class="badge bg-purple-100 text-purple-800 rounded-pill px-2 py-1"
                                            style="font-size: 0.7rem;">
                                            {{ $periodeInfo->nama_periode }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>{{ $p->mahasiswa_nama }}</td>
                                <td>{{ $p->mahasiswa_nim }}</td>
                                <td>{{ $p->kelas ?? '-' }}</td>
                                <td>{{ $p->mata_kuliah ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-purple-100 text-purple-800 rounded-pill px-3 py-1">
                                        {{ number_format($p->rata_rata, 2) }}
                                    </span>
                                </td>
                                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info rounded-pill view-detail-btn"
                                        style="color: white; background-color: #0dcaf0; border-color: #0dcaf0;"
                                        data-id="{{ $p->id }}" data-mahasiswa="{{ $p->mahasiswa_nama }}"
                                        data-nim="{{ $p->mahasiswa_nim }}" data-kelas="{{ $p->kelas }}"
                                        data-mata_kuliah="{{ $p->mata_kuliah }}"
                                        data-periode="{{ $periodeInfo ? $periodeInfo->nama_periode : '-' }}"
                                        data-nilai='@json($p->nilai)' data-rata="{{ $p->rata_rata }}"
                                        data-tanggal="{{ $p->created_at->format('d/m/Y H:i') }}">
                                        <i class="bi bi-eye me-1"></i> Lihat Jawaban
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada penilaian untuk dosen ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $penilaian->links('pagination::bootstrap-5') }}
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
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted">Mahasiswa</small>
                                <div class="fw-semibold" id="detailMahasiswa">-</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted">NIM</small>
                                <div class="fw-semibold" id="detailNim">-</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted">Periode</small>
                                <div class="fw-semibold" id="detailPeriode">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted">Kelas</small>
                                <div class="fw-semibold" id="detailKelas">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted">Mata Kuliah</small>
                                <div class="fw-semibold" id="detailMataKuliah">-</div>
                            </div>
                        </div>
                    </div>
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
                                    <td colspan="5" class="text-center py-4">
                                        <div class="spinner-border text-purple-600" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
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
        // Chart untuk dosen
        let chartData = @json($chartData);
        let labels = Object.keys(chartData);
        let persepsi = labels.map(d => chartData[d].persepsi);
        let harapan = labels.map(d => chartData[d].harapan);

        let ctx = document.getElementById('dosenChart').getContext('2d');
        new Chart(ctx, {
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
                maintainAspectRatio: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, max: 5, title: { display: true, text: 'Skor (1-5)' } } }
            }
        });

        // Fungsi untuk memformat data jawaban
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

        // Event listener untuk tombol Lihat Jawaban
        document.querySelectorAll('.view-detail-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('detailMahasiswa').innerText = this.dataset.mahasiswa;
                document.getElementById('detailNim').innerText = this.dataset.nim;
                document.getElementById('detailPeriode').innerText = this.dataset.periode || '-';
                document.getElementById('detailKelas').innerText = this.dataset.kelas || '-';
                document.getElementById('detailMataKuliah').innerText = this.dataset.mata_kuliah || '-';

                let nilai = this.dataset.nilai;
                if (typeof nilai === 'string') {
                    try { nilai = JSON.parse(nilai); } catch (e) { }
                }

                const jawabanArray = formatJawaban(nilai);
                const tbody = document.getElementById('detailJawabanBody');
                tbody.innerHTML = '';

                if (jawabanArray.length > 0) {
                    jawabanArray.forEach((item, index) => {
                        const harapan = item.harapan;
                        const persepsi = item.persepsi;
                        const gap = (persepsi - harapan).toFixed(2);
                        const gapClass = gap >= 0 ? 'text-success' : 'text-danger';
                        tbody.innerHTML += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${item.teks}</td>
                                <td class="text-center">${harapan}</td>
                                <td class="text-center">${persepsi}</td>
                                <td class="text-center ${gapClass} fw-bold">${gap}</td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data jawaban</td></tr>';
                }

                new bootstrap.Modal(document.getElementById('detailJawabanModal')).show();
            });
        });
    </script>
@endpush