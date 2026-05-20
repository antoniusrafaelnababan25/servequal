@extends('layouts.admin')

@section('title', 'Tambah Mahasiswa - Admin')
@section('page_title', 'Tambah Mahasiswa Baru')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
                    <form id="createMahasiswaForm">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6"><label class="form-label fw-semibold">Nama Lengkap <span
                                        class="text-danger">*</span></label><input type="text" name="name"
                                    class="form-control bg-light border-0 rounded-3" required></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Username <span
                                        class="text-danger">*</span></label><input type="text" name="username"
                                    class="form-control bg-light border-0 rounded-3" required></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Email <span
                                        class="text-danger">*</span></label><input type="email" name="email"
                                    class="form-control bg-light border-0 rounded-3" required></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">NIM <span
                                        class="text-danger">*</span></label><input type="text" name="nim"
                                    class="form-control bg-light border-0 rounded-3" required></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Jurusan <span
                                        class="text-danger">*</span></label>
                                <select name="jurusan_id" id="jurusan_id" class="form-select bg-light border-0 rounded-3"
                                    required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach($jurusanList as $jurusan)
                                        <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Program Studi <span
                                        class="text-danger">*</span></label>
                                <select name="prodi_id" id="prodi_id" class="form-select bg-light border-0 rounded-3"
                                    required>
                                    <option value="">Pilih Prodi</option>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Kelas</label><input type="text"
                                    name="kelas" class="form-control bg-light border-0 rounded-3"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Tanggal Lahir</label><input
                                    type="date" name="tanggal_lahir" class="form-control bg-light border-0 rounded-3"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Password <span
                                        class="text-danger">*</span></label><input type="password" name="password"
                                    class="form-control bg-light border-0 rounded-3" required></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Konfirmasi Password <span
                                        class="text-danger">*</span></label><input type="password"
                                    name="password_confirmation" class="form-control bg-light border-0 rounded-3" required>
                            </div>
                        </div>
                        <div class="mt-5 d-flex gap-3 justify-content-end">
                            <a href="{{ route('admin.mahasiswa.index') }}"
                                class="btn btn-secondary rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-purple rounded-pill px-4">Simpan Mahasiswa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('jurusan_id').addEventListener('change', function () {
            let jurusanId = this.value;
            let prodiSelect = document.getElementById('prodi_id');
            if (jurusanId) {
                axios.get('/admin/mahasiswa/prodi-by-jurusan/' + jurusanId)
                    .then(response => {
                        prodiSelect.innerHTML = '<option value="">Pilih Prodi</option>';
                        response.data.forEach(prodi => prodiSelect.innerHTML += `<option value="${prodi.id}">${prodi.nama_prodi}</option>`);
                    });
            } else prodiSelect.innerHTML = '<option value="">Pilih Prodi</option>';
        });

        document.getElementById('createMahasiswaForm').addEventListener('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            axios.post('{{ route("admin.mahasiswa.store") }}', formData)
                .then(response => { if (response.data.success) Swal.fire('Berhasil', response.data.message, 'success').then(() => window.location.href = '{{ route("admin.mahasiswa.index") }}'); })
                .catch(error => { if (error.response && error.response.data.errors) { let msg = Object.values(error.response.data.errors).flat().join('\n'); Swal.fire('Validasi Gagal', msg, 'error'); } else Swal.fire('Error', 'Terjadi kesalahan', 'error'); });
        });
    </script>
@endpush