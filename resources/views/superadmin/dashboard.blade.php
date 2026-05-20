@extends('layouts.superadmin')

@section('title', 'Dashboard Super Admin - SERVQUAL POLMED')
@section('page_title', 'Dashboard Monitoring Kepuasan Layanan')

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
                    <form method="GET" action="{{ route('super.dashboard') }}" id="periodeForm">
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

        <!-- Statistik Cards Row 1 -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6">
                <div class="card-hover bg-white rounded-4 shadow-sm p-3 h-100 border-0"
                    style="background: linear-gradient(135deg, #ffffff 0%, #f8f4ff 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Super Admin</p>
                            <h3 class="mb-0 fw-bold text-purple-800 display-6">
                                {{ number_format($userStats['super_admin']) }}
                            </h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(76, 29, 149, 0.1);">
                            <i class="bi bi-shield-lock-fill text-purple-600 fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-hover bg-white rounded-4 shadow-sm p-3 h-100 border-0"
                    style="background: linear-gradient(135deg, #ffffff 0%, #f8f4ff 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Admin</p>
                            <h3 class="mb-0 fw-bold text-purple-600 display-6">{{ number_format($userStats['admin']) }}</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(76, 29, 149, 0.1);">
                            <i class="bi bi-shield-lock text-purple-600 fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-hover bg-white rounded-4 shadow-sm p-3 h-100 border-0"
                    style="background: linear-gradient(135deg, #ffffff 0%, #e8f5e9 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Total Dosen</p>
                            <h3 class="mb-0 fw-bold text-success display-6">{{ number_format($userStats['dosen']) }}</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(46, 125, 50, 0.1);">
                            <i class="bi bi-person-badge text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-hover bg-white rounded-4 shadow-sm p-3 h-100 border-0"
                    style="background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase">Total Mahasiswa</p>
                            <h3 class="mb-0 fw-bold text-info display-6">{{ number_format($userStats['mahasiswa']) }}</h3>
                        </div>
                        <div class="rounded-circle p-3" style="background: rgba(2, 136, 209, 0.1);">
                            <i class="bi bi-people text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Cards Row 2 -->


        <!-- Chart SERVQUAL Gap -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-semibold mb-0"><i class="bi bi-graph-up me-2 text-purple-600"></i>Analisis Gap
                            SERVQUAL (Persepsi vs Harapan)</h5>
                        <span class="badge bg-purple-100 text-purple-800 px-3 py-2 rounded-pill">Metode SERVQUAL</span>
                    </div>
                    <div class="position-relative">
                        <canvas id="gapChart" height="120" style="max-height: 400px; width: 100%;"></canvas>
                    </div>
                    <div class="text-muted small mt-3 text-center">
                        <i class="bi bi-info-circle"></i> *Gap negatif menunjukkan layanan di bawah harapan
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar User dengan Filter -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <h5 class="fw-semibold mb-0"><i class="bi bi-people-fill me-2 text-purple-600"></i>Daftar Pengguna</h5>
                <form method="GET" action="{{ route('super.dashboard') }}" id="filterForm"
                    class="d-flex flex-wrap gap-2 align-items-center">
                    <select name="role" class="form-select form-select-sm bg-light border-0 rounded-pill"
                        style="width: 130px;" onchange="this.form.submit()">
                        <option value="">Semua Role</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin
                        </option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="dosen" {{ request('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    </select>
                    <select name="jenjang" class="form-select form-select-sm bg-light border-0 rounded-pill"
                        style="width: 140px;" onchange="this.form.submit()">
                        <option value="">Semua Jenjang</option>
                        @foreach($jenjangList as $key => $label)
                            <option value="{{ $key }}" {{ request('jenjang') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="jurusan_id" class="form-select form-select-sm bg-light border-0 rounded-pill"
                        style="width: 160px;" onchange="this.form.submit()">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $jurusan)
                            <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                {{ $jurusan->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                    <select name="prodi_id" class="form-select form-select-sm bg-light border-0 rounded-pill"
                        style="width: 200px;" onchange="this.form.submit()">
                        <option value="">Semua Prodi</option>
                        @foreach($prodiList as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_prodi }}
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group input-group-sm" style="width: 260px;">
                        <input type="text" name="search" class="form-control bg-light border-0 rounded-start-pill"
                            placeholder="Cari nama / email" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary rounded-end-pill" type="submit"><i
                                class="bi bi-search"></i></button>
                    </div>
                    @if(request()->anyFilled(['role', 'jenjang', 'jurusan_id', 'prodi_id', 'search']))
                        <a href="{{ route('super.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Program Studi</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="fw-medium">{{ $user->name }}</td>
                                <td>{{ $user->username ?? '-' }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $roleColors = [
                                            'super_admin' => 'danger',
                                            'admin' => 'purple',
                                            'dosen' => 'success',
                                            'mahasiswa' => 'info'
                                        ];
                                        $color = $roleColors[$user->role] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}-100 text-{{ $color }}-800 rounded-pill px-3 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td>{{ $user->prodi->nama_prodi ?? '-' }}</td>
                                <td>{{ $user->prodi->jurusan->nama_jurusan ?? '-' }}</td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-purple toggle-active rounded-pill px-3"
                                        data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                        data-active="{{ $user->is_active ? '1' : '0' }}">
                                        <i class="bi bi-arrow-repeat me-1"></i>Toggle
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">Tidak ada data user</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Data dari server
        let chartDataFromServer = @json($chartData);

        console.log('Data dari server:', chartDataFromServer);

        // Gunakan data dari server atau data dummy jika kosong
        let chartData = chartDataFromServer;

        if (!chartData || Object.keys(chartData).length === 0 ||
            (Object.values(chartData).every(item => item.persepsi === 0 && item.harapan === 0))) {
            chartData = {
                'Tangible': { persepsi: 0, harapan: 0, gap: 0 },
                'Reliability': { persepsi: 0, harapan: 0, gap: 0 },
                'Responsiveness': { persepsi: 0, harapan: 0, gap: 0 },
                'Assurance': { persepsi: 0, harapan: 0, gap: 0 },
                'Empathy': { persepsi: 0, harapan: 0, gap: 0 }
            };
            console.log('Menggunakan data default (kosong)');
        }

        let labels = Object.keys(chartData);
        let persepsiValues = labels.map(function (d) { return chartData[d].persepsi; });
        let harapanValues = labels.map(function (d) { return chartData[d].harapan; });

        console.log('Labels:', labels);
        console.log('Persepsi:', persepsiValues);
        console.log('Harapan:', harapanValues);

        // Render chart
        let ctx = document.getElementById('gapChart').getContext('2d');
        let chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Persepsi (Kinerja)',
                        data: persepsiValues,
                        backgroundColor: '#8b5cf6',
                        borderRadius: 8,
                        barPercentage: 0.65,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Harapan',
                        data: harapanValues,
                        backgroundColor: '#c084fc',
                        borderRadius: 8,
                        barPercentage: 0.65,
                        categoryPercentage: 0.8
                    }
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
                                    let gap = (value - harapanValues[index]).toFixed(2);
                                    return label + ': ' + value + ' (Gap: ' + gap + ')';
                                }
                                return label + ': ' + value;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10
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

        // Toggle user status
        document.querySelectorAll('.toggle-active').forEach(function (btn) {
            btn.addEventListener('click', function () {
                let userId = this.dataset.id;
                let userName = this.dataset.name;
                let isActive = this.dataset.active === '1';

                Swal.fire({
                    title: 'Ubah status ' + userName + '?',
                    text: (isActive ? 'Nonaktifkan' : 'Aktifkan') + ' user ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4c1d95',
                    confirmButtonText: 'Ya, ubah',
                    cancelButtonText: 'Batal'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        axios.post('/super-admin/users/' + userId + '/toggle-active')
                            .then(function (res) {
                                if (res.data.success) {
                                    Swal.fire('Berhasil', res.data.message, 'success').then(function () {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal', res.data.message, 'error');
                                }
                            })
                            .catch(function (error) {
                                console.error(error);
                                Swal.fire('Error', 'Terjadi kesalahan', 'error');
                            });
                    }
                });
            });
        });

        // Handle periode form submit
        document.getElementById('periodeForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            let url = new URL(window.location.href);
            let params = new URLSearchParams(new FormData(this));
            url.search = params.toString();
            window.location.href = url.toString();
        });
    </script>
@endpush