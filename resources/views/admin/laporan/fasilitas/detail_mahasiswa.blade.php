@extends('layouts.admin')

@section('title', 'Detail Mahasiswa - Penilaian Fasilitas')
@section('page_title', 'Detail Mahasiswa')

@section('content')
    <div class="container-fluid px-0">
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between mb-4">
                <h5 class="fw-semibold mb-0"><i class="bi bi-person me-2 text-success"></i>Detail Mahasiswa</h5>
                <a href="{{ route('admin.laporan.fasilitas.index') }}" class="btn btn-secondary rounded-pill"><i
                        class="bi bi-arrow-left me-1"></i>Kembali</a>
            </div>

            <!-- Info Mahasiswa -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <div class="text-muted small">Nama</div>
                        <div class="fw-semibold">{{ $mahasiswa->name }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <div class="text-muted small">NIM</div>
                        <div class="fw-semibold">{{ $mahasiswa->nim ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <div class="text-muted small">Kelas</div>
                        <div class="fw-semibold">{{ $mahasiswa->kelas ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-light rounded-3 p-3">
                        <div class="text-muted small">Rata-rata</div>
                        <div class="fw-semibold text-success">{{ number_format($statistik['rata_rata'], 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Statistik -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-3 text-center">
                        <div class="text-muted small">Total Penilaian</div>
                        <h4 class="fw-bold text-success mb-0">{{ number_format($statistik['total']) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-3 text-center">
                        <div class="text-muted small">Tertinggi</div>
                        <h4 class="fw-bold text-warning mb-0">{{ number_format($statistik['tertinggi'], 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white border rounded-3 p-3 text-center">
                        <div class="text-muted small">Terendah</div>
                        <h4 class="fw-bold text-danger mb-0">{{ number_format($statistik['terendah'], 2) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Chart Gap -->
            <div class="bg-light rounded-3 p-3 mb-4">
                <canvas id="mahasiswaChart" height="250"></canvas>
            </div>

            <!-- Riwayat Penilaian dengan Jawaban Langsung -->
            <h5 class="fw-semibold mb-3">Riwayat Penilaian Fasilitas</h5>

            @forelse($penilaian as $index => $p)
                <div class="card mb-4 border-0 rounded-4 shadow-sm overflow-hidden">
                    <div class="card-header bg-success bg-opacity-10 border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <i class="bi bi-calendar-week me-2 text-success"></i>
                                <strong>Periode:</strong>
                                @php $periode = \App\Models\KuesionerPeriode::find($p->periode_id); @endphp
                                @if($periode)
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success ms-1">{{ $periode->nama_periode }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                            <div>
                                <i class="bi bi-star-fill me-1 text-warning"></i>
                                <strong>Rata-rata:</strong>
                                <span class="badge bg-success ms-1">{{ number_format($p->rata_rata, 2) }}</span>
                            </div>
                            <div>
                                <i class="bi bi-clock me-1 text-muted"></i>
                                <small class="text-muted">{{ $p->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
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
                                <tbody id="jawaban-{{ $p->id }}">
                                    <tr>
                                        <td colspan="6" class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                                            Memuat jawaban...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-light text-center py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                    Belum ada penilaian fasilitas untuk mahasiswa ini
                </div>
            @endforelse

            <div class="mt-3 d-flex justify-content-end">
                {{ $penilaian->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart untuk mahasiswa
        let chartData = @json($chartData);
        let labels = Object.keys(chartData).map(k => chartData[k].label);
        let persepsi = Object.keys(chartData).map(k => chartData[k].persepsi);
        let harapan = Object.keys(chartData).map(k => chartData[k].harapan);

        new Chart(document.getElementById('mahasiswaChart'), {
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
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        title: { display: true, text: 'Skor (1-5)' }
                    }
                }
            }
        });

        // Fungsi untuk memuat jawaban setiap penilaian
        function loadAllJawaban() {
            let penilaianIds = @json($penilaian->pluck('id'));

            penilaianIds.forEach(function (penilaianId) {
                axios.get('/admin/laporan-fasilitas/jawaban/' + penilaianId)
                    .then(res => {
                        if (res.data.success) {
                            let jawaban = res.data.data.jawaban;
                            let tbody = document.getElementById('jawaban-' + penilaianId);
                            tbody.innerHTML = '';

                            if (jawaban && jawaban.length > 0) {
                                jawaban.forEach((item, idx) => {
                                    let gap = item.gap;
                                    let gapClass = gap >= 0 ? 'text-success' : 'text-danger';
                                    let gapText = gap >= 0 ? '+' + gap : gap.toString();

                                    tbody.innerHTML += `
                                        <tr>
                                            <td class="text-center">${idx + 1}</td>
                                            <td>${item.pertanyaan}</td>
                                            <td class="text-center">${item.kategori || '-'}</td>
                                            <td class="text-center">${item.harapan}</td>
                                            <td class="text-center">${item.persepsi}</td>
                                            <td class="text-center ${gapClass} fw-bold">${gapText}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            <i class="bi bi-inbox"></i> Tidak ada data jawaban
                                        </td>
                                    </tr>
                                `;
                            }
                        } else {
                            document.getElementById('jawaban-' + penilaianId).innerHTML = `
                                <tr>
                                    <td colspan="6" class="text-center text-danger py-3">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Gagal memuat data
                                    </td>
                                </tr>
                            `;
                        }
                    })
                    .catch(err => {
                        console.error('Error loading jawaban for penilaian ' + penilaianId, err);
                        document.getElementById('jawaban-' + penilaianId).innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center text-danger py-3">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Terjadi kesalahan
                                </td>
                            </tr>
                        `;
                    });
            });
        }

        // Load semua jawaban saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function () {
            loadAllJawaban();
        });
    </script>
@endpush