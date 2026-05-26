@extends('layouts.superadmin')

@section('title', 'Manajemen Jurusan - Super Admin')
@section('page_title', 'Manajemen Jurusan')

@section('content')
    <div class="container-fluid px-0">
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-building-fill me-2 text-purple-600"></i>Daftar Jurusan
                    </h5>
                    <p class="text-muted small mt-2 mb-0">Kelola data jurusan yang terdaftar di sistem</p>
                </div>
                <a href="{{ route('super.jurusan.create') }}" class="btn btn-purple rounded-pill px-4">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Jurusan
                </a>
            </div>

            <!-- Filter -->
            <div class="card border-0 bg-light rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('super.jurusan.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-10">
                                <label class="form-label fw-semibold">Pencarian</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control bg-white border-0 rounded-3"
                                        placeholder="Cari nama jurusan..." value="{{ request('search') }}">
                                    <button class="btn btn-purple rounded-3" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    @if(request()->filled('search'))
                                        <a href="{{ route('super.jurusan.index') }}" class="btn btn-secondary rounded-3">
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

            <!-- Tabel Jurusan -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Nama Jurusan</th>
                            <th width="35%">Deskripsi</th>
                            <th width="15%">Jumlah Prodi</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jurusan as $index => $item)
                            <tr>
                                <td class="text-center">{{ $jurusan->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-medium">{{ $item->nama_jurusan }}</div>
                                    <small class="text-muted">Slug: {{ $item->slug }}</small>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        {{ Str::limit($item->deskripsi ?? '-', 100) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">
                                        <i class="bi bi-book me-1"></i>{{ $item->prodi_count ?? 0 }} Program Studi
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('super.jurusan.show', $item->id) }}"
                                            class="btn btn-outline-info rounded-pill me-1" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('super.jurusan.edit', $item->id) }}"
                                            class="btn btn-outline-purple rounded-pill me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-outline-danger rounded-pill delete-jurusan"
                                            data-id="{{ $item->id }}" data-name="{{ $item->nama_jurusan }}"
                                            data-prodi-count="{{ $item->prodi_count ?? 0 }}" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data jurusan
                                    <div class="mt-3">
                                        <a href="{{ route('super.jurusan.create') }}"
                                            class="btn btn-purple btn-sm rounded-pill">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah Jurusan Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Menampilkan {{ $jurusan->firstItem() }} - {{ $jurusan->lastItem() }} dari {{ $jurusan->total() }} data
                </div>
                <div>
                    {{ $jurusan->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
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
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Delete Jurusan
            document.querySelectorAll('.delete-jurusan').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const prodiCount = parseInt(this.dataset.prodiCount);

                    let warningText = `Apakah Anda yakin ingin menghapus jurusan <strong>${name}</strong>?`;
                    if (prodiCount > 0) {
                        warningText = `Jurusan <strong>${name}</strong> memiliki ${prodiCount} program studi.<br><br>
                                   <span class="text-danger">Tidak dapat menghapus jurusan yang masih memiliki program studi!</span>`;
                    }

                    Swal.fire({
                        title: 'Hapus Jurusan?',
                        html: warningText,
                        icon: prodiCount > 0 ? 'error' : 'warning',
                        showCancelButton: prodiCount === 0,
                        confirmButtonColor: prodiCount > 0 ? '#d33' : '#d33',
                        confirmButtonText: prodiCount > 0 ? 'OK' : 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed && prodiCount === 0) {
                            Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                            axios.delete(`/super-admin/jurusan/${id}`)
                                .then(res => {
                                    Swal.close();
                                    if (res.data.success) {
                                        Swal.fire('Terhapus!', res.data.message, 'success').then(() => location.reload());
                                    } else {
                                        Swal.fire('Gagal', res.data.message, 'error');
                                    }
                                })
                                .catch(err => {
                                    Swal.close();
                                    Swal.fire('Error', err.response?.data?.message || 'Terjadi kesalahan', 'error');
                                });
                        }
                    });
                });
            });
        });
    </script>
@endpush