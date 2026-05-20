@extends('layouts.superadmin')

@section('title', 'Manajemen User - SUPER ADMIN')
@section('page_title', 'Manajemen User')

@section('content')
    <div class="container-fluid px-0">
        <!-- Header dengan Tombol Tambah & Filter -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <a href="{{ route('super.users.create') }}" class="btn btn-purple rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Tambah User
            </a>

            <form method="GET" action="{{ route('super.users.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 160px;">
                    <span class="input-group-text bg-white border rounded-start-pill ps-3 pe-2">
                        <i class="bi bi-funnel-fill text-purple-800"></i>
                    </span>
                    <select name="role" class="form-select form-select-sm bg-white border-0 rounded-end-pill"
                        onchange="this.form.submit()">
                        <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>Semua Role</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin
                        </option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="dosen" {{ request('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    </select>
                </div>

                <div class="input-group input-group-sm" style="width: 260px;">
                    <input type="text" name="search" class="form-control bg-white border rounded-start-pill"
                        placeholder="Cari nama, email, NIDN, NIM..." value="{{ request('search') }}">
                    <button class="btn btn-purple rounded-end-pill px-3" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                @if(request('role') != 'all' || request('search'))
                    <a href="{{ route('super.users.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel User -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>NIDN/NIM</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Jurusan/Prodi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                                            <tr>
                                                <td class="fw-medium">
                                                    {{ $user->name }}
                                                    @if($user->kelas)
                                                        <br><small class="text-muted">Kelas: {{ $user->kelas }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->role == 'dosen')
                                                        <span class="badge bg-info bg-opacity-10 text-info">NIDN: {{ $user->nidn ?? '-' }}</span>
                                                    @elseif($user->role == 'mahasiswa')
                                                        <span class="badge bg-success bg-opacity-10 text-success">NIM:
                                                            {{ $user->nim ?? '-' }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @php
                                                        $roleBadge = [
                                                            'super_admin' => 'danger',
                                                            'admin' => 'warning',
                                                            'dosen' => 'info',
                                                            'mahasiswa' => 'success'
                                                        ];
                                                    @endphp
                             <span
                                                        class="badge bg-{{ $roleBadge[$user->role] ?? 'secondary' }} bg-opacity-10 text-{{ $roleBadge[$user->role] ?? 'secondary' }} rounded-pill px-3 py-1">
                                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>
                                                        @if($user->prodi)
                                                            <div>{{ $user->prodi->jurusan->nama_jurusan ?? '-' }}</div>
                                                            <div class="text-muted">{{ $user->prodi->nama_prodi ?? '-' }}</div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </small>
                                                </td>
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
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('super.users.show', $user->id) }}"
                                                            class="btn btn-outline-info rounded-pill me-1" title="Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('super.users.edit', $user->id) }}"
                                                            class="btn btn-outline-warning rounded-pill me-1" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger rounded-pill delete-user"
                                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary rounded-pill toggle-status"
                                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                            data-status="{{ $user->is_active }}"
                                                            title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                            <i class="bi bi-{{ $user->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    Tidak ada data user
                                </td>
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
    <script>
        // Delete user
        document.querySelectorAll('.delete-user').forEach(btn => {
            btn.addEventListener('click', function () {
                let userId = this.dataset.id;
                let userName = this.dataset.name;

                Swal.fire({
                    title: 'Hapus user ' + userName + '?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete('/super-admin/users/' + userId)
                            .then(response => {
                                if (response.data.success) {
                                    Swal.fire('Terhapus!', response.data.message, 'success').then(() => location.reload());
                                } else {
                                    Swal.fire('Gagal', response.data.message, 'error');
                                }
                            })
                            .catch(error => {
                                let message = error.response?.data?.message || 'Terjadi kesalahan';
                                Swal.fire('Gagal', message, 'error');
                            });
                    }
                });
            });
        });

        // Toggle status user
        document.querySelectorAll('.toggle-status').forEach(btn => {
            btn.addEventListener('click', function () {
                let userId = this.dataset.id;
                let userName = this.dataset.name;
                let currentStatus = this.dataset.status;
                let newStatus = currentStatus == 1 ? 'nonaktifkan' : 'aktifkan';

                Swal.fire({
                    title: `${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)} ${userName}?`,
                    text: `Anda akan ${newStatus} user ini.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4c1d95',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: `Ya, ${newStatus}!`,
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('/super-admin/users/' + userId + '/toggle-active')
                            .then(response => {
                                if (response.data.success) {
                                    Swal.fire('Berhasil!', response.data.message, 'success').then(() => location.reload());
                                } else {
                                    Swal.fire('Gagal', response.data.message, 'error');
                                }
                            })
                            .catch(error => {
                                let message = error.response?.data?.message || 'Terjadi kesalahan';
                                Swal.fire('Gagal', message, 'error');
                            });
                    }
                });
            });
        });
    </script>
@endpush