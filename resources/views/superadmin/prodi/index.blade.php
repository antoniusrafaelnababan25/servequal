@extends('layouts.superadmin')

@section('title', 'Manajemen Program Studi - Super Admin')
@section('page_title', 'Manajemen Program Studi')

@section('content')
    <div class="container-fluid px-0">
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-book-fill me-2 text-purple-600"></i>Daftar Program Studi
                    </h5>
                    <p class="text-muted small mt-2 mb-0">Kelola data program studi yang terdaftar di sistem</p>
                </div>
                <button type="button" class="btn btn-purple rounded-pill px-4" data-bs-toggle="modal"
                    data-bs-target="#createProdiModal">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Prodi
                </button>
            </div>

            <!-- Filter -->
            <div class="card border-0 bg-light rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('super.prodi.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Filter Jurusan</label>
                                <select name="jurusan_id" class="form-select bg-white border-0 rounded-3"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Jurusan</option>
                                    @foreach($jurusanList as $jurusan)
                                        <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                            {{ $jurusan->nama_jurusan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pencarian</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control bg-white border-0 rounded-3"
                                        placeholder="Cari nama prodi..." value="{{ request('search') }}">
                                    <button class="btn btn-purple rounded-3" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    @if(request()->filled('search') || request()->filled('jurusan_id'))
                                        <a href="{{ route('super.prodi.index') }}" class="btn btn-secondary rounded-3">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <label class="form-label fw-semibold">&nbsp;</label>
                                <button type="button" class="btn btn-outline-secondary rounded-pill w-100"
                                    onclick="window.location.reload()">
                                    <i class="bi bi-arrow-repeat me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Prodi -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Program Studi</th>
                            <th width="15%">Jurusan</th>
                            <th width="10%">Jenjang</th>
                            <th width="15%">Slug</th>
                            <th width="10%">Status</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prodi as $index => $item)
                            <tr>
                                <td class="text-center">{{ $prodi->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-medium">{{ $item->nama_prodi }}</div>
                                </td>
                                <td>{{ $item->jurusan->nama_jurusan ?? '-' }}</td>
                                <td>
                                    @php
                                        $jenjangColors = [
                                            'sarjana' => 'primary',
                                            'pascasarjana' => 'success',
                                            'internasional' => 'info'
                                        ];
                                        $color = $jenjangColors[$item->jenjang] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}-100 text-{{ $color }}-800 rounded-pill px-3 py-1">
                                        {{ ucfirst($item->jenjang) }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $item->slug }}</small></td>
                                <td>
                                    @if($item->is_active)
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
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('super.prodi.show', $item->id) }}"
                                            class="btn btn-outline-info rounded-pill me-1" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('super.prodi.edit', $item->id) }}"
                                            class="btn btn-outline-purple rounded-pill me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-outline-danger rounded-pill delete-prodi"
                                            data-id="{{ $item->id }}" data-name="{{ $item->nama_prodi }}" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data program studi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Menampilkan {{ $prodi->firstItem() }} - {{ $prodi->lastItem() }} dari {{ $prodi->total() }} data
                </div>
                <div>
                    {{ $prodi->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create Prodi -->
    <div class="modal fade" id="createProdiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-purple-100 border-0">
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-book-plus me-2 text-purple-600"></i>Tambah Program Studi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createProdiForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jurusan <span class="text-danger">*</span></label>
                            <select name="jurusan_id" class="form-select bg-light border-0 rounded-3" required>
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Program Studi <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_prodi" class="form-control bg-light border-0 rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenjang <span class="text-danger">*</span></label>
                            <select name="jenjang" class="form-select bg-light border-0 rounded-3" required>
                                <option value="">Pilih Jenjang</option>
                                <option value="sarjana">Sarjana (S1)</option>
                                <option value="pascasarjana">Pascasarjana (S2)</option>
                                <option value="internasional">Internasional</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1"
                                    checked>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    <i class="bi bi-check-circle-fill me-1 text-success"></i>Aktifkan Prodi
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-purple rounded-pill px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .bg-purple-100 {
            background-color: #f3e8ff;
        }

        .text-purple-600 {
            color: #7c3aed;
        }

        .btn-purple {
            background-color: #4c1d95;
            border-color: #4c1d95;
            color: white;
        }

        .btn-purple:hover {
            background-color: #3b1580;
            border-color: #3b1580;
            color: white;
        }

        .btn-outline-purple {
            color: #4c1d95;
            border-color: #4c1d95;
        }

        .btn-outline-purple:hover {
            background-color: #4c1d95;
            color: white;
        }

        .bg-primary-100 {
            background-color: #e0f2fe;
        }

        .text-primary-800 {
            color: #075985;
        }

        .bg-success-100 {
            background-color: #dcfce7;
        }

        .text-success-800 {
            color: #166534;
        }

        .bg-info-100 {
            background-color: #e0f2fe;
        }

        .text-info-800 {
            color: #075985;
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Create Prodi
            document.getElementById('createProdiForm')?.addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

                axios.post('{{ route("super.prodi.store") }}', new FormData(this))
                    .then(res => {
                        if (res.data.success) {
                            Swal.fire('Berhasil!', 'Program Studi berhasil ditambahkan', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', res.data.message, 'error');
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Simpan';
                    })
                    .catch(err => {
                        if (err.response?.data?.errors) {
                            let msg = Object.values(err.response.data.errors).flat().join('\n');
                            Swal.fire('Validasi Gagal', msg, 'error');
                        } else {
                            Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Simpan';
                    });
            });

            // Delete Prodi
            document.querySelectorAll('.delete-prodi').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const name = this.dataset.name;

                    Swal.fire({
                        title: 'Hapus Program Studi?',
                        html: `Apakah Anda yakin ingin menghapus program studi <strong>${name}</strong>?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed) {
                            axios.delete(`/super-admin/prodi/${id}`)
                                .then(res => {
                                    if (res.data.success) {
                                        Swal.fire('Terhapus!', res.data.message, 'success').then(() => location.reload());
                                    } else {
                                        Swal.fire('Gagal', res.data.message, 'error');
                                    }
                                })
                                .catch(err => {
                                    Swal.fire('Error', err.response?.data?.message || 'Terjadi kesalahan', 'error');
                                });
                        }
                    });
                });
            });
        });
    </script>
@endpush