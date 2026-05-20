@extends('layouts.admin')

@section('title', 'Manajemen Dosen - Admin')
@section('page_title', 'Manajemen Dosen')

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <a href="{{ route('admin.dosen.create') }}" class="btn btn-purple rounded-pill px-4">
                <i class="bi bi-plus-circle me-2"></i>Tambah Dosen
            </a>

            <form method="GET" action="{{ route('admin.dosen.index') }}" class="d-flex flex-wrap gap-2 align-items-center"
                id="filterForm">
                <select name="jurusan_id" class="form-select form-select-sm bg-light border-0 rounded-pill"
                    style="width: 180px;">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $jurusan)
                        <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                            {{ $jurusan->nama_jurusan }}</option>
                    @endforeach
                </select>

                <select name="prodi_id" class="form-select form-select-sm bg-light border-0 rounded-pill"
                    style="width: 200px;">
                    <option value="">Semua Prodi</option>
                    @foreach($prodiList as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>

                <select name="jenjang" class="form-select form-select-sm bg-light border-0 rounded-pill"
                    style="width: 150px;">
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangList as $key => $label)
                        <option value="{{ $key }}" {{ request('jenjang') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <div class="input-group input-group-sm" style="width: 260px;">
                    <input type="text" name="search" class="form-control bg-light border-0 rounded-start-pill"
                        placeholder="Cari nama / nidn / email" value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary rounded-end-pill" type="submit"><i
                            class="bi bi-search"></i></button>
                </div>

                @if(request('jurusan_id') || request('prodi_id') || request('jenjang') || request('search'))
                    <a href="{{ route('admin.dosen.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
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
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>NIDN</th>
                            <th>Program Studi</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dosen as $d)
                            <tr>
                                <td class="fw-medium">{{ $d->name }}</td>
                                <td>{{ $d->username ?? '-' }}</td>
                                <td>{{ $d->email }}</td>
                                <td>{{ $d->nidn ?? '-' }}</td>
                                <td>{{ $d->prodi->nama_prodi ?? '-' }}</td>
                                <td>{{ $d->prodi->jurusan->nama_jurusan ?? '-' }}</td>
                                <td>
                                    @if($d->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1"><i
                                                class="bi bi-check-circle-fill me-1"></i>Aktif</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1"><i
                                                class="bi bi-x-circle-fill me-1"></i>Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.dosen.show', $d->id) }}"
                                            class="btn btn-outline-info rounded-pill me-1" title="Detail"><i
                                                class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.dosen.edit', $d->id) }}"
                                            class="btn btn-outline-warning rounded-pill me-1" title="Edit"><i
                                                class="bi bi-pencil"></i></a>
                                        <button class="btn btn-outline-danger rounded-pill me-1 delete-dosen"
                                            data-id="{{ $d->id }}" data-name="{{ $d->name }}" title="Hapus"><i
                                                class="bi bi-trash"></i></button>
                                        <button class="btn btn-outline-purple rounded-pill toggle-active" data-id="{{ $d->id }}"
                                            data-name="{{ $d->name }}" data-active="{{ $d->is_active ? '1' : '0' }}"
                                            title="Toggle Status"><i class="bi bi-arrow-repeat"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">Tidak ada data dosen</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $dosen->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Cascading dropdown jurusan -> prodi
        document.querySelector('select[name="jurusan_id"]').addEventListener('change', function () {
            let jurusanId = this.value;
            let prodiSelect = document.querySelector('select[name="prodi_id"]');
            if (jurusanId) {
                axios.get('/admin/dosen/prodi-by-jurusan/' + jurusanId)
                    .then(response => {
                        prodiSelect.innerHTML = '<option value="">Semua Prodi</option>';
                        response.data.forEach(prodi => {
                            prodiSelect.innerHTML += `<option value="${prodi.id}">${prodi.nama_prodi}</option>`;
                        });
                    });
            } else {
                prodiSelect.innerHTML = '<option value="">Semua Prodi</option>';
            }
        });

        // Hapus Dosen
        document.querySelectorAll('.delete-dosen').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id, name = this.dataset.name;
                Swal.fire({ title: `Hapus dosen ${name}?`, text: "Data tidak dapat dikembalikan!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' }).then(result => {
                    if (result.isConfirmed) {
                        axios.delete(`/admin/dosen/${id}`).then(res => { if (res.data.success) Swal.fire('Terhapus!', res.data.message, 'success').then(() => location.reload()); else Swal.fire('Gagal', res.data.message, 'error'); }).catch(() => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                    }
                });
            });
        });

        // Toggle aktif
        document.querySelectorAll('.toggle-active').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id, name = this.dataset.name, isActive = this.dataset.active === '1';
                Swal.fire({ title: `Ubah status ${name}?`, text: (isActive ? 'Nonaktifkan' : 'Aktifkan') + ' dosen ini?', icon: 'question', showCancelButton: true, confirmButtonColor: '#4c1d95', confirmButtonText: 'Ya, ubah', cancelButtonText: 'Batal' }).then(result => {
                    if (result.isConfirmed) {
                        axios.post(`/admin/dosen/${id}/toggle-active`).then(res => { if (res.data.success) Swal.fire('Berhasil', res.data.message, 'success').then(() => location.reload()); else Swal.fire('Gagal', res.data.message, 'error'); }).catch(() => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                    }
                });
            });
        });
    </script>
@endpush