@extends('layouts.superadmin')

@section('title', 'Tambah User - SUPER ADMIN')
@section('page_title', 'Tambah User Baru')

@section('content')
    <div class="container-fluid px-0">
        <div class="mb-3">
            <a href="{{ route('super.users.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="bg-white rounded-4 shadow-sm p-4">
            <form id="createUserForm" action="{{ route('super.users.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                    <select name="role" id="roleSelect" class="form-select rounded-3" required>
                        <option value="">Pilih Role</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="dosen">Dosen</option>
                        <option value="mahasiswa">Mahasiswa</option>
                    </select>
                </div>

                <!-- Field untuk Super Admin & Admin -->
                <div id="adminFields" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Konfirmasi Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3">
                        </div>
                    </div>
                </div>

                <!-- Field untuk Dosen -->
                <div id="dosenFields" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">NIDN <span class="text-danger">*</span></label>
                            <input type="text" name="nidn" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jurusan</label>
                            <select name="jurusan" id="jurusan_dosen" class="form-select rounded-3">
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Program Studi</label>
                            <select name="prodi_id" id="prodi_dosen" class="form-select rounded-3" disabled>
                                <option value="">-- Pilih Jurusan Terlebih Dahulu --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Username (Optional)</label>
                            <input type="text" name="username" class="form-control rounded-3"
                                placeholder="Kosongkan untuk menggunakan NIDN">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Password (Optional)</label>
                            <input type="password" name="password" class="form-control rounded-3"
                                placeholder="Kosongkan untuk menggunakan NIDN">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3">
                        </div>
                    </div>
                </div>

                <!-- Field untuk Mahasiswa -->
                <div id="mahasiswaFields" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">NIM <span class="text-danger">*</span></label>
                            <input type="text" name="nim" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kelas</label>
                            <input type="text" name="kelas" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jurusan</label>
                            <select name="jurusan" id="jurusan_mahasiswa" class="form-select rounded-3">
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Program Studi</label>
                            <select name="prodi_id" id="prodi_mahasiswa" class="form-select rounded-3" disabled>
                                <option value="">-- Pilih Jurusan Terlebih Dahulu --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Username (Optional)</label>
                            <input type="text" name="username" class="form-control rounded-3"
                                placeholder="Kosongkan untuk menggunakan NIM">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Password (Optional)</label>
                            <input type="password" name="password" class="form-control rounded-3"
                                placeholder="Kosongkan untuk menggunakan NIM">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control rounded-3">
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" checked>
                        <label class="form-check-label" for="isActive">Aktif</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-purple rounded-pill px-4">
                        <i class="bi bi-save me-2"></i>Simpan
                    </button>
                    <a href="{{ route('super.users.index') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        (function () {
            const roleSelect = document.getElementById('roleSelect');
            const adminFields = document.getElementById('adminFields');
            const dosenFields = document.getElementById('dosenFields');
            const mahasiswaFields = document.getElementById('mahasiswaFields');

            const jurusanDosen = document.getElementById('jurusan_dosen');
            const prodiDosen = document.getElementById('prodi_dosen');
            const jurusanMahasiswa = document.getElementById('jurusan_mahasiswa');
            const prodiMahasiswa = document.getElementById('prodi_mahasiswa');

            function toggleFieldsByRole(role) {
                adminFields.style.display = 'none';
                dosenFields.style.display = 'none';
                mahasiswaFields.style.display = 'none';

                if (role === 'super_admin' || role === 'admin') {
                    adminFields.style.display = 'block';
                } else if (role === 'dosen') {
                    dosenFields.style.display = 'block';
                } else if (role === 'mahasiswa') {
                    mahasiswaFields.style.display = 'block';
                }
            }

            function loadProdi(jurusanId, targetSelect) {
                if (!jurusanId) {
                    targetSelect.innerHTML = '<option value="">-- Pilih Jurusan Terlebih Dahulu --</option>';
                    targetSelect.disabled = true;
                    return;
                }

                targetSelect.innerHTML = '<option value="">Memuat data...</option>';
                targetSelect.disabled = true;

                // Gunakan axios seperti di Admin Mahasiswa
                axios.get('/super-admin/users/prodi-by-jurusan/' + jurusanId)
                    .then(response => {
                        targetSelect.innerHTML = '<option value="">Pilih Program Studi</option>';
                        response.data.forEach(prodi => {
                            targetSelect.innerHTML += `<option value="${prodi.id}">${prodi.nama_prodi} (${prodi.jenjang})</option>`;
                        });
                        targetSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error loading prodi:', error);
                        targetSelect.innerHTML = '<option value="">Error loading data</option>';
                        targetSelect.disabled = false;
                    });
            }

            // Event listeners
            if (roleSelect) {
                roleSelect.addEventListener('change', function () {
                    toggleFieldsByRole(this.value);
                });
            }

            if (jurusanDosen) {
                jurusanDosen.addEventListener('change', function () {
                    loadProdi(this.value, prodiDosen);
                });
            }

            if (jurusanMahasiswa) {
                jurusanMahasiswa.addEventListener('change', function () {
                    loadProdi(this.value, prodiMahasiswa);
                });
            }

            // Form submission
            const form = document.getElementById('createUserForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    axios.post(this.action, formData)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire('Berhasil!', response.data.message, 'success').then(() => {
                                    window.location.href = '{{ route("super.users.index") }}';
                                });
                            }
                        })
                        .catch(error => {
                            if (error.response && error.response.status === 422) {
                                let msg = Object.values(error.response.data.errors).flat().join('\n');
                                Swal.fire('Validasi Gagal', msg, 'error');
                            } else {
                                Swal.fire('Error', 'Terjadi kesalahan', 'error');
                            }
                        });
                });
            }
        })();
    </script>
@endpush