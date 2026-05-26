@extends('layouts.superadmin')

@section('title', 'Tambah Jurusan - Super Admin')
@section('page_title', 'Tambah Jurusan')

@section('content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super.jurusan.index') }}"
                                    class="text-purple">Jurusan</a></li>
                            <li class="breadcrumb-item active">Tambah Jurusan</li>
                        </ol>
                    </nav>

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-purple-100 rounded-circle p-3 me-3">
                            <i class="bi bi-building-add text-purple-600 fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-0">Form Tambah Jurusan</h5>
                            <p class="text-muted small mb-0">Isi data jurusan baru</p>
                        </div>
                    </div>

                    <form id="createJurusanForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Jurusan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jurusan" class="form-control bg-light border-0 rounded-3" required
                                autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" rows="4" class="form-control bg-light border-0 rounded-3"></textarea>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-purple rounded-pill px-5">Simpan</button>
                            <a href="{{ route('super.jurusan.index') }}"
                                class="btn btn-secondary rounded-pill px-4">Kembali</a>
                        </div>
                    </form>
                </div>
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
    </style>
@endsection

@push('scripts')
    <script>
        document.getElementById('createJurusanForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            axios.post('{{ route("super.jurusan.store") }}', new FormData(this))
                .then(res => {
                    if (res.data.success) {
                        Swal.fire('Berhasil!', res.data.message, 'success').then(() => {
                            window.location.href = '{{ route("super.jurusan.index") }}';
                        });
                    } else {
                        Swal.fire('Gagal!', res.data.message, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Simpan';
                    }
                })
                .catch(err => {
                    if (err.response?.data?.errors) {
                        let msg = Object.values(err.response.data.errors).flat().join('\n');
                        Swal.fire('Validasi Gagal', msg, 'error');
                    } else {
                        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Simpan';
                });
        });
    </script>
@endpush