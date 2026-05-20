@extends('layouts.admin')

@section('title', 'Tambah Dosen - Admin')
@section('page_title', 'Tambah Dosen Baru')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
                    <form id="createDosenForm">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control bg-light border-0 rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control bg-light border-0 rounded-3"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control bg-light border-0 rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIDN</label>
                                <input type="text" name="nidn" class="form-control bg-light border-0 rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jurusan <span class="text-danger">*</span></label>
                                <select name="jurusan_id" id="jurusan_id" class="form-select bg-light border-0 rounded-3"
                                    required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach($jurusanList as $jurusan)
                                        <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Program Studi <span
                                        class="text-danger">*</span></label>
                                <select name="prodi_id" id="prodi_id" class="form-select bg-light border-0 rounded-3"
                                    required>
                                    <option value="">Pilih Prodi</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control bg-light border-0 rounded-3"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Konfirmasi Password <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation"
                                    class="form-control bg-light border-0 rounded-3" required>
                            </div>
                        </div>
                        <div class="mt-5 d-flex gap-3 justify-content-end">
                            <a href="{{ route('admin.dosen.index') }}" class="btn btn-secondary rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-purple rounded-pill px-4">Simpan Dosen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Cascading dropdown jurusan -> prodi
        document.getElementById('jurusan_id').addEventListener('change', function () {
            let jurusanId = this.value;
            let prodiSelect = document.getElementById('prodi_id');
            if (jurusanId) {
                axios.get('/admin/dosen/prodi-by-jurusan/' + jurusanId)
                    .then(response => {
                        prodiSelect.innerHTML = '<option value="">Pilih Prodi</option>';
                        response.data.forEach(prodi => {
                            prodiSelect.innerHTML += `<option value="${prodi.id}">${prodi.nama_prodi}</option>`;
                        });
                    });
            } else {
                prodiSelect.innerHTML = '<option value="">Pilih Prodi</option>';
            }
        });

        document.getElementById('createDosenForm').addEventListener('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            axios.post('{{ route("admin.dosen.store") }}', formData)
                .then(response => {
                    if (response.data.success) {
                        Swal.fire('Berhasil', response.data.message, 'success').then(() => window.location.href = '{{ route("admin.dosen.index") }}');
                    }
                })
                .catch(error => {
                    if (error.response && error.response.data.errors) {
                        let errors = error.response.data.errors;
                        let errorMsg = '';
                        for (let key in errors) errorMsg += errors[key][0] + '\n';
                        Swal.fire('Validasi Gagal', errorMsg, 'error');
                    } else {
                        Swal.fire('Error', 'Terjadi kesalahan', 'error');
                    }
                });
        });
    </script>
@endpush