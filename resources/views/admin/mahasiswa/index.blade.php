@extends('layouts.admin')

@section('title', 'Manajemen Mahasiswa - Admin')
@section('page_title', 'Manajemen Mahasiswa')

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <a href="{{ route('admin.mahasiswa.create') }}" class="btn btn-purple rounded-pill px-4">
                <i class="bi bi-plus-circle me-2"></i>Tambah Mahasiswa
            </a>

            <form method="GET" action="{{ route('admin.mahasiswa.index') }}"
                class="d-flex flex-wrap gap-2 align-items-center" id="filterForm">
                <select name="jurusan_id" class="form-select form-select-sm bg-light border-0 rounded-pill"
                    style="width: 180px;" onchange="this.form.submit()">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $jurusan)
                        <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                            {{ $jurusan->nama_jurusan }}</option>
                    @endforeach
                </select>
                <select name="prodi_id" class="form-select form-select-sm bg-light border-0 rounded-pill"
                    style="width: 200px;" onchange="this.form.submit()">
                    <option value="">Semua Prodi</option>
                    @foreach($prodiList as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
                <select name="jenjang" class="form-select form-select-sm bg-light border-0 rounded-pill"
                    style="width: 140px;" onchange="this.form.submit()">
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangList as $key => $label)
                        <option value="{{ $key }}" {{ request('jenjang') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="text" name="kelas" class="form-control form-control-sm bg-light border-0 rounded-pill"
                    placeholder="Kelas" style="width: 100px;" value="{{ request('kelas') }}" onblur="this.form.submit()">
                <div class="input-group input-group-sm" style="width: 260px;">
                    <input type="text" name="search" class="form-control bg-light border-0 rounded-start-pill"
                        placeholder="Cari nama / nim / email" value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary rounded-end-pill" type="submit"><i
                            class="bi bi-search"></i></button>
                </div>
                @if(request()->anyFilled(['jurusan_id', 'prodi_id', 'jenjang', 'kelas', 'search']))
                    <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3"><i
                            class="bi bi-x-circle me-1"></i>Reset</a>
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
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th>Jurusan</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswa as $m)
                            <tr>
                                <td class="fw-medium">{{ $m->name }}</td>
                                <td>{{ $m->username ?? '-' }}</td>
                                <td>{{ $m->email }}</td>
                                <td>{{ $m->nim ?? '-' }}</td>
                                <td>{{ $m->prodi->nama_prodi ?? '-' }}</td>
                                <td>{{ $m->prodi->jurusan->nama_jurusan ?? '-' }}</td>
                                <td>{{ $m->kelas ?? '-' }}</td>
                                <td>@if($m->is_active)<span
                                    class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1"><i
                                class="bi bi-check-circle-fill me-1"></i>Aktif</span>@else<span
                                                class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1"><i
                                            class="bi bi-x-circle-fill me-1"></i>Nonaktif</span>@endif</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.mahasiswa.show', $m->id) }}"
                                            class="btn btn-outline-info rounded-pill me-1" title="Detail"><i
                                                class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.mahasiswa.edit', $m->id) }}"
                                            class="btn btn-outline-warning rounded-pill me-1" title="Edit"><i
                                                class="bi bi-pencil"></i></a>
                                        <button class="btn btn-outline-danger rounded-pill me-1 delete-mahasiswa"
                                            data-id="{{ $m->id }}" data-name="{{ $m->name }}" title="Hapus"><i
                                                class="bi bi-trash"></i></button>
                                        <button class="btn btn-outline-purple rounded-pill toggle-active" data-id="{{ $m->id }}"
                                            data-name="{{ $m->name }}" data-active="{{ $m->is_active ? '1' : '0' }}"
                                            title="Toggle Status"><i class="bi bi-arrow-repeat"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">Tidak ada data mahasiswa</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $mahasiswa->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Delete mahasiswa
        document.querySelectorAll('.delete-mahasiswa').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id, name = this.dataset.name;
                Swal.fire({ title: `Hapus mahasiswa ${name}?`, text: "Data tidak dapat dikembalikan!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' }).then(result => {
                    if (result.isConfirmed) {
                        axios.delete(`/admin/mahasiswa/${id}`).then(res => { if (res.data.success) Swal.fire('Terhapus!', res.data.message, 'success').then(() => location.reload()); else Swal.fire('Gagal', res.data.message, 'error'); }).catch(() => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                    }
                });
            });
        });

        // Toggle aktif
        document.querySelectorAll('.toggle-active').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id, name = this.dataset.name, isActive = this.dataset.active === '1';
                Swal.fire({ title: `Ubah status ${name}?`, text: (isActive ? 'Nonaktifkan' : 'Aktifkan') + ' mahasiswa ini?', icon: 'question', showCancelButton: true, confirmButtonColor: '#4c1d95', confirmButtonText: 'Ya, ubah', cancelButtonText: 'Batal' }).then(result => {
                    if (result.isConfirmed) axios.post(`/admin/mahasiswa/${id}/toggle-active`).then(res => { if (res.data.success) Swal.fire('Berhasil', res.data.message, 'success').then(() => location.reload()); else Swal.fire('Gagal', res.data.message, 'error'); }).catch(() => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                });
            });
        });
    </script>
@endpush