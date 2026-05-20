@extends('layouts.profile')

@section('title', 'Profile - ' . ucfirst(Auth::user()->role) . ' | SERVQUAL POLMED')
@section('page_title', 'Profile Saya')

@section('content')
    <div class="profile-card p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="position-relative d-inline-block">
                @if($user->avatar && file_exists(storage_path('app/public/' . $user->avatar)))
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="avatar-preview" id="avatarPreview">
                @else
                    <div class="avatar-circle">
                        <i class="bi bi-person-fill"></i>
                    </div>
                @endif
                <div class="role-badge">
                    <span class="badge bg-purple-100 text-purple-800">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                </div>
            </div>
            <h3 class="mt-3 mb-1">{{ $user->name }}</h3>
            <p class="text-muted">{{ $user->email }}</p>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs justify-content-center mb-4" id="profileTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button"
                    role="tab">
                    <i class="bi bi-person me-2"></i>Informasi Pribadi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button"
                    role="tab">
                    <i class="bi bi-lock me-2"></i>Ubah Password
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="avatar-tab" data-bs-toggle="tab" data-bs-target="#avatar" type="button"
                    role="tab">
                    <i class="bi bi-image me-2"></i>Foto Profil
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="danger-tab" data-bs-toggle="tab" data-bs-target="#danger" type="button"
                    role="tab">
                    <i class="bi bi-exclamation-triangle me-2"></i>Hapus Akun
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Tab 1: Informasi Pribadi -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                    @csrf
                    @method('patch')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-3"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control rounded-3"
                                value="{{ old('username', $user->username) }}" required>
                            @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control rounded-3"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control rounded-3"
                                value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}">
                            @error('tanggal_lahir') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    @if($user->role == 'dosen')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NIDN</label>
                                <input type="text" class="form-control rounded-3" value="{{ $user->nidn ?? '-' }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Jurusan</label>
                                <input type="text" class="form-control rounded-3"
                                    value="{{ $user->prodi->jurusan->nama_jurusan ?? $user->jurusan ?? '-' }}" disabled>
                            </div>
                        </div>
                    @endif

                    @if($user->role == 'mahasiswa')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NIM</label>
                                <input type="text" class="form-control rounded-3" value="{{ $user->nim ?? '-' }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kelas</label>
                                <input type="text" class="form-control rounded-3" value="{{ $user->kelas ?? '-' }}" disabled>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <button type="submit" class="btn btn-purple rounded-pill px-4">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 2: Ubah Password -->
            <div class="tab-pane fade" id="password" role="tabpanel">
                <form method="POST" action="{{ route('profile.password.update') }}" id="passwordForm">
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control rounded-3" required>
                        @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="password" class="form-control rounded-3" required>
                        <small class="text-muted">Minimal 8 karakter</small>
                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" required>
                    </div>
                    <button type="submit" class="btn btn-purple rounded-pill px-4">
                        <i class="bi bi-shield-lock me-2"></i>Update Password
                    </button>
                </form>
            </div>

            <!-- Tab 3: Foto Profil -->
            <div class="tab-pane fade" id="avatar" role="tabpanel">
                <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data"
                    id="avatarForm">
                    @csrf
                    @method('put')

                    <div class="text-center mb-4">
                        @if($user->avatar && file_exists(storage_path('app/public/' . $user->avatar)))
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="avatar-preview mb-3"
                                id="currentAvatar" style="width: 150px; height: 150px;">
                        @else
                            <div class="avatar-circle mb-3" style="width: 150px; height: 150px;">
                                <i class="bi bi-person-fill" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Foto Profil Baru</label>
                        <input type="file" name="avatar" class="form-control rounded-3" accept="image/*" id="avatarInput">
                        <small class="text-muted">Format: JPG, PNG, GIF (Max: 2MB)</small>
                        @error('avatar') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-purple rounded-pill px-4">
                            <i class="bi bi-upload me-2"></i>Upload Foto
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 4: Hapus Akun -->
            <div class="tab-pane fade" id="danger" role="tabpanel">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Peringatan!</strong> Menghapus akun akan menghapus semua data Anda secara permanen.
                </div>

                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteForm">
                    @csrf
                    @method('delete')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Konfirmasi Password</label>
                        <input type="password" name="password" class="form-control rounded-3"
                            placeholder="Masukkan password Anda untuk konfirmasi" required>
                    </div>

                    <button type="button" class="btn btn-danger rounded-pill px-4" id="deleteAccountBtn">
                        <i class="bi bi-trash me-2"></i>Hapus Akun Saya
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Preview avatar sebelum upload
        document.getElementById('avatarInput')?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const preview = document.getElementById('currentAvatar');
                    if (preview) {
                        preview.src = event.target.result;
                    } else {
                        const avatarContainer = document.querySelector('#avatar .text-center');
                        const oldPreview = avatarContainer.querySelector('img, .avatar-circle');
                        if (oldPreview) {
                            oldPreview.remove();
                        }
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.classList.add('avatar-preview', 'mb-3');
                        img.style.width = '150px';
                        img.style.height = '150px';
                        avatarContainer.insertBefore(img, avatarContainer.firstChild);
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Konfirmasi hapus akun
        document.getElementById('deleteAccountBtn')?.addEventListener('click', function () {
            Swal.fire({
                title: 'Hapus Akun?',
                text: 'Apakah Anda yakin ingin menghapus akun ini? Semua data akan hilang permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        });

        // Tampilkan pesan sukses dari session
        @if(session('success'))
            Swal.fire('Berhasil!', '{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            Swal.fire('Error!', '{{ session('error') }}', 'error');
        @endif
    </script>
@endpush