@extends('layouts.admin')

@section('title', 'Manajemen Pertanyaan - Admin')
@section('page_title', 'Manajemen Pertanyaan')

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h5 class="fw-semibold mb-0">
                            <i class="bi bi-question-circle-fill me-2 text-purple-600"></i>Daftar Pertanyaan
                        </h5>
                        <p class="text-muted small mt-2 mb-0">Kelola pertanyaan untuk penilaian dosen dan fasilitas</p>
                    </div>
                    <a href="{{ route('admin.pertanyaan.create') }}" class="btn btn-purple rounded-pill px-4">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Pertanyaan
                    </a>
                </div>

                <!-- Filter -->
                <div class="card border-0 bg-light rounded-4 mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.pertanyaan.index') }}" id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Tipe Penilaian</label>
                                    <select name="tipe_penilaian" class="form-select filter-select bg-white border-0 rounded-3">
                                        <option value="">Semua Tipe</option>
                                        @foreach($tipePenilaianList as $key => $label)
                                            <option value="{{ $key }}" {{ request('tipe_penilaian') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Dimensi</label>
                                    <select name="dimensi" class="form-select filter-select bg-white border-0 rounded-3">
                                        <option value="">Semua Dimensi</option>
                                        @foreach($dimensiList as $key => $label)
                                            <option value="{{ $key }}" {{ request('dimensi') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Target Responden</label>
                                    <select name="target_role" class="form-select filter-select bg-white border-0 rounded-3">
                                        <option value="">Semua Target</option>
                                        @foreach($targetRoleList as $key => $label)
                                            <option value="{{ $key }}" {{ request('target_role') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select name="status" class="form-select filter-select bg-white border-0 rounded-3">
                                        <option value="">Semua</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Cari</label>
                                    <input type="text" name="search" class="form-control bg-white border-0 rounded-3" 
                                           placeholder="Cari pertanyaan..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-purple rounded-pill px-4">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                    @if(request()->anyFilled(['tipe_penilaian', 'dimensi', 'target_role', 'status', 'search']))
                                        <a href="{{ route('admin.pertanyaan.index') }}" class="btn btn-secondary rounded-pill px-4">
                                            <i class="bi bi-x-circle me-1"></i>Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Pertanyaan</th>
                                <th width="10%">Dimensi</th>
                                <th width="12%">Tipe Penilaian</th>
                                <th width="12%">Target</th>
                                <th width="8%">Kategori</th>
                                <th width="8%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pertanyaan as $index => $item)
                            <tr>
                                <td>{{ $pertanyaan->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-medium">{{ Str::limit($item->teks, 60) }}</div>
                                    <small class="text-muted">ID: #{{ $item->id }}</small>
                                    <br>
                                    <small class="text-muted">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</small>
                                 </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                                        {{ $item->dimensi }}
                                    </span>
                                 </td>
                                <td>
                                    @if($item->tipe_penilaian == 'penilaian_dosen')
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                            <i class="bi bi-person-badge me-1"></i>Penilaian Dosen
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                            <i class="bi bi-building me-1"></i>Penilaian Fasilitas
                                        </span>
                                    @endif
                                 </td>
                                <td>
                                    {!! $item->target_role_badge !!}
                                 </td>
                                <td>
                                    @if($item->kategori_fasilitas)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">
                                            {{ ucfirst($item->kategori_fasilitas) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                 </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input toggle-active" 
                                               data-id="{{ $item->id }}"
                                               data-url="{{ route('admin.pertanyaan.toggle-active', $item->id) }}"
                                               {{ $item->is_active ? 'checked' : '' }}>
                                    </div>
                                 </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.pertanyaan.show', $item->id) }}" 
                                           class="btn btn-outline-info rounded-pill me-1" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.pertanyaan.edit', $item->id) }}" 
                                           class="btn btn-outline-purple rounded-pill me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-outline-danger rounded-pill delete-question" 
                                                data-id="{{ $item->id }}" 
                                                data-teks="{{ Str::limit($item->teks, 50) }}"
                                                data-url="{{ route('admin.pertanyaan.destroy', $item->id) }}"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                 </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data pertanyaan
                                    <div class="mt-3">
                                        <a href="{{ route('admin.pertanyaan.create') }}" class="btn btn-purple btn-sm rounded-pill">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah Pertanyaan
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
                        Menampilkan {{ $pertanyaan->firstItem() }} - {{ $pertanyaan->lastItem() }} 
                        dari {{ $pertanyaan->total() }} data
                    </div>
                    <div>{{ $pertanyaan->withQueryString()->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-purple-600 { color: #4c1d95; }
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
    .form-check-input:checked {
        background-color: #4c1d95;
        border-color: #4c1d95;
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Active
    document.querySelectorAll('.toggle-active').forEach(toggle => {
        toggle.addEventListener('change', function(e) {
            e.preventDefault();
            const url = this.dataset.url;
            const isChecked = this.checked;
            const originalState = !isChecked;
            
            Swal.fire({
                title: 'Ubah Status?',
                text: isChecked ? 'Aktifkan pertanyaan ini?' : 'Nonaktifkan pertanyaan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4c1d95',
                confirmButtonText: 'Ya, ubah',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    
                    axios.post(url)
                        .then(res => {
                            Swal.close();
                            if (res.data.success) {
                                Swal.fire('Berhasil', res.data.message, 'success');
                            } else {
                                this.checked = originalState;
                                Swal.fire('Gagal', res.data.message, 'error');
                            }
                        })
                        .catch(() => {
                            Swal.close();
                            this.checked = originalState;
                            Swal.fire('Error', 'Terjadi kesalahan', 'error');
                        });
                } else {
                    this.checked = originalState;
                }
            });
        });
    });

    // Delete Question
    document.querySelectorAll('.delete-question').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const teks = this.dataset.teks;
            const url = this.dataset.url;
            
            Swal.fire({
                title: 'Hapus Pertanyaan?',
                html: `Apakah Anda yakin ingin menghapus pertanyaan:<br><strong>"${teks}"</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    
                    axios.delete(url)
                        .then(res => {
                            Swal.close();
                            if (res.data.success) {
                                Swal.fire('Terhapus!', res.data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Gagal', res.data.message, 'error');
                            }
                        })
                        .catch(() => {
                            Swal.close();
                            Swal.fire('Error', 'Terjadi kesalahan', 'error');
                        });
                }
            });
        });
    });
});
</script>
@endpush