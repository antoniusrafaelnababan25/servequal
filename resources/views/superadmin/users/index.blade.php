@extends('layouts.superadmin')

@section('title', 'Manajemen User - Super Admin')
@section('page_title', 'Manajemen User')

@section('content')
    <div class="container-fluid px-0">
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-people-fill me-2 text-purple-600"></i>Daftar User
                    </h5>
                    <p class="text-muted small mt-2 mb-0">Kelola semua user (Super Admin, Admin, Dosen, Mahasiswa)</p>
                </div>
                <button type="button" class="btn btn-purple rounded-pill px-4" data-bs-toggle="modal"
                    data-bs-target="#createUserModal">
                    <i class="bi bi-plus-circle me-2"></i>Tambah User
                </button>
            </div>

            <!-- Filter -->
            <div class="card border-0 bg-light rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('super.users.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Role</label>
                                <select name="role" class="form-select bg-white border-0 rounded-3"
                                    onchange="this.form.submit()">
                                    <option value="all">Semua Role</option>
                                    <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super
                                        Admin</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="dosen" {{ request('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                    <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Pencarian</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control bg-white border-0 rounded-3"
                                        placeholder="Cari nama, email, username, nidn, nim..."
                                        value="{{ request('search') }}">
                                    <button class="btn btn-purple rounded-3" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    @if(request()->filled('search') || request()->filled('role'))
                                        <a href="{{ route('super.users.index') }}" class="btn btn-secondary rounded-3">
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

            <!-- Tabel User -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Nama</th>
                            <th width="15%">Username</th>
                            <th width="20%">Email</th>
                            <th width="10%">Role</th>
                            <th width="10%">Program Studi</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td class="text-center">{{ $users->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-medium">{{ $user->name }}</div>
                                    @if($user->nidn)
                                        <small class="text-muted">NIDN: {{ $user->nidn }}</small>
                                    @endif
                                    @if($user->nim)
                                        <small class="text-muted">NIM: {{ $user->nim }}</small>
                                    @endif
                                </td>
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
                                            class="btn btn-outline-purple rounded-pill me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-outline-danger rounded-pill delete-user"
                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                            data-role="{{ $user->role }}" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data user
                                </td </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} data
                </div>
                <div>
                    {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create User -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-purple-100 border-0">
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-person-plus-fill me-2 text-purple-600"></i>Tambah User Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createUserForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control bg-light border-0 rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control bg-light border-0 rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-select bg-light border-0 rounded-3" required>
                                    <option value="">Pilih Role</option>
                                    <option value="super_admin">Super Admin</option>
                                    <option value="admin">Admin</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" name="username" class="form-control bg-light border-0 rounded-3">
                                <small class="text-muted">Kosongkan untuk auto-generate</small>
                            </div>

                            <!-- Field untuk Dosen -->
                            <div id="dosenFields" style="display: none;">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIDN <span class="text-danger">*</span></label>
                                    <input type="text" name="nidn" class="form-control bg-light border-0 rounded-3">
                                </div>
                            </div>

                            <!-- Field untuk Mahasiswa -->
                            <div id="mahasiswaFields" style="display: none;">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIM <span class="text-danger">*</span></label>
                                    <input type="text" name="nim" class="form-control bg-light border-0 rounded-3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kelas</label>
                                    <input type="text" name="kelas" class="form-control bg-light border-0 rounded-3">
                                </div>
                            </div>

                            <!-- Field untuk Dosen & Mahasiswa -->
                            <div id="civitasFields" style="display: none;">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jurusan</label>
                                    <select name="jurusan" id="jurusan" class="form-select bg-light border-0 rounded-3">
                                        <option value="">Pilih Jurusan</option>
                                        @foreach($jurusans as $jurusan)
                                            <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Program Studi</label>
                                    <select name="prodi_id" id="prodi_id" class="form-select bg-light border-0 rounded-3">
                                        <option value="">Pilih Program Studi</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control bg-light border-0 rounded-3">
                                <small class="text-muted">Minimal 6 karakter. Kosongkan untuk auto-generate</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation"
                                    class="form-control bg-light border-0 rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control bg-light border-0 rounded-3">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input type="checkbox" class="form-check-input" name="is_active" id="is_active"
                                        value="1" checked>
                                    <label class="form-check-label fw-semibold" for="is_active">
                                        <i class="bi bi-check-circle-fill me-1 text-success"></i>Aktifkan User
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-purple rounded-pill px-4">Simpan User</button>
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

        .text-purple-800 {
            color: #4c1d95;
        }

        .btn-purple {
            background-color: #4c1d95;
            border-color: #4c1d95;
            color: white;
        }

        .btn-purple:hover {
            background-color: #3b156b;
            border-color: #3b156b;
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
        document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.getElementById('role');
            const dosenFields = document.getElementById('dosenFields');
            const mahasiswaFields = document.getElementById('mahasiswaFields');
            const civitasFields = document.getElementById('civitasFields');
            const jurusanSelect = document.getElementById('jurusan');
            const prodiSelect = document.getElementById('prodi_id');

            // Toggle fields based on role
            function toggleFields() {
                const role = roleSelect.value;

                dosenFields.style.display = 'none';
                mahasiswaFields.style.display = 'none';
                civitasFields.style.display = 'none';

                if (role === 'dosen') {
                    dosenFields.style.display = 'block';
                    civitasFields.style.display = 'block';
                } else if (role === 'mahasiswa') {
                    mahasiswaFields.style.display = 'block';
                    civitasFields.style.display = 'block';
                }
            }

            roleSelect.addEventListener('change', toggleFields);

            // Load prodi based on jurusan
            jurusanSelect.addEventListener('change', function () {
                const jurusanId = this.value;
                prodiSelect.innerHTML = '<option value="">Pilih Program Studi</option>';

                if (jurusanId) {
                    axios.get('/super-admin/users/prodi-by-jurusan/' + jurusanId)
                        .then(res => {
                            res.data.forEach(prodi => {
                                prodiSelect.innerHTML += `<option value="${prodi.id}">${prodi.nama_prodi} (${prodi.jenjang})</option>`;
                            });
                        })
                        .catch(err => console.error(err));
                }
            });

            // Create User Form Submit
            document.getElementById('createUserForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

                Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                axios.post('{{ route("super.users.store") }}', new FormData(this), {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                })
                    .then(res => {
                        Swal.close();
                        if (res.data.success) {
                            Swal.fire('Berhasil!', res.data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', res.data.message, 'error');
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Simpan User';
                    })
                    .catch(err => {
                        Swal.close();
                        if (err.response?.data?.errors) {
                            let msg = Object.values(err.response.data.errors).flat().join('\n');
                            Swal.fire('Validasi Gagal', msg, 'error');
                        } else {
                            Swal.fire('Error!', err.response?.data?.message || 'Terjadi kesalahan', 'error');
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Simpan User';
                    });
            });

            // Delete User
            document.querySelectorAll('.delete-user').forEach(btn => {
                btn.addEventListener('click', function () {
                    const userId = this.dataset.id;
                    const userName = this.dataset.name;
                    const userRole = this.dataset.role;

                    if (userRole === 'super_admin') {
                        Swal.fire('Peringatan', 'Tidak dapat menghapus user Super Admin', 'warning');
                        return;
                    }

                    Swal.fire({
                        title: 'Hapus User?',
                        html: `Apakah Anda yakin ingin menghapus user <strong>${userName}</strong>?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed) {
                            Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                            axios.delete(`/super-admin/users/${userId}`)
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