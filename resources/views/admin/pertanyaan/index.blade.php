@extends('layouts.admin')

@section('title', 'Manajemen Pertanyaan - Admin')
@section('page_title', 'Manajemen Pertanyaan SERVQUAL')

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <a href="{{ route('admin.pertanyaan.create') }}" class="btn btn-purple rounded-pill px-4">
                <i class="bi bi-plus-circle me-2"></i>Tambah Pertanyaan
            </a>

            <form method="GET" action="{{ route('admin.pertanyaan.index') }}"
                class="d-flex flex-wrap gap-2 align-items-center">
                <select name="dimensi" class="form-select form-select-sm bg-light border-0 rounded-pill"
                    style="width: 140px;" onchange="this.form.submit()">
                    <option value="">Semua Dimensi</option>
                    @foreach($dimensiList as $dim)
                        <option value="{{ $dim }}" {{ request('dimensi') == $dim ? 'selected' : '' }}>{{ $dim }}</option>
                    @endforeach
                </select>
                <select name="target_role" class="form-select form-select-sm bg-light border-0 rounded-pill"
                    style="width: 140px;" onchange="this.form.submit()">
                    <option value="">Semua Target</option>
                    <option value="mahasiswa" {{ request('target_role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ request('target_role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                </select>
                <select name="is_active" class="form-select form-select-sm bg-light border-0 rounded-pill"
                    style="width: 120px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @if(request()->anyFilled(['dimensi', 'target_role', 'is_active']))
                    <a href="{{ route('admin.pertanyaan.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Dimensi</th>
                            <th>Pertanyaan</th>
                            <th>Target Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pertanyaan as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td><span
                                        class="badge bg-purple-100 text-purple-800 rounded-pill px-3 py-1">{{ $p->dimensi }}</span>
                                </td>
                                <td>{{ $p->teks }}</td>
                                <td>{{ $p->target_role == 'mahasiswa' ? 'Mahasiswa' : 'Dosen' }}</td>
                                <td>@if($p->is_active)<span
                                    class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1"><i
                                class="bi bi-check-circle-fill me-1"></i>Aktif</span>@else<span
                                                class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1"><i
                                            class="bi bi-x-circle-fill me-1"></i>Nonaktif</span>@endif</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.pertanyaan.show', $p->id) }}"
                                            class="btn btn-outline-info rounded-pill me-1" title="Detail"><i
                                                class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.pertanyaan.edit', $p->id) }}"
                                            class="btn btn-outline-warning rounded-pill me-1" title="Edit"><i
                                                class="bi bi-pencil"></i></a>
                                        <button class="btn btn-outline-danger rounded-pill me-1 delete-pertanyaan"
                                            data-id="{{ $p->id }}" data-teks="{{ Str::limit($p->teks, 30) }}" title="Hapus"><i
                                                class="bi bi-trash"></i></button>
                                        <button class="btn btn-outline-purple rounded-pill toggle-active" data-id="{{ $p->id }}"
                                            data-active="{{ $p->is_active ? '1' : '0' }}" title="Toggle Status"><i
                                                class="bi bi-arrow-repeat"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Tidak ada data pertanyaan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $pertanyaan->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Hapus pertanyaan
        document.querySelectorAll('.delete-pertanyaan').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id, teks = this.dataset.teks;
                Swal.fire({ title: `Hapus pertanyaan?`, text: `"${teks}" akan dihapus`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' }).then(result => {
                    if (result.isConfirmed) {
                        axios.delete(`/admin/pertanyaan/${id}`).then(res => { if (res.data.success) Swal.fire('Terhapus!', res.data.message, 'success').then(() => location.reload()); else Swal.fire('Gagal', res.data.message, 'error'); }).catch(() => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                    }
                });
            });
        });

        // Toggle aktif
        document.querySelectorAll('.toggle-active').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id, isActive = this.dataset.active === '1';
                Swal.fire({ title: `Ubah status?`, text: (isActive ? 'Nonaktifkan' : 'Aktifkan') + ' pertanyaan ini?', icon: 'question', showCancelButton: true, confirmButtonColor: '#4c1d95', confirmButtonText: 'Ya, ubah', cancelButtonText: 'Batal' }).then(result => {
                    if (result.isConfirmed) axios.post(`/admin/pertanyaan/${id}/toggle-active`).then(res => { if (res.data.success) Swal.fire('Berhasil', res.data.message, 'success').then(() => location.reload()); else Swal.fire('Gagal', res.data.message, 'error'); }).catch(() => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                });
            });
        });
    </script>
@endpush