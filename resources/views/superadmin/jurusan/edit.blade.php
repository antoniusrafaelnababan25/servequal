@extends('layouts.superadmin')

@section('title', 'Edit Jurusan - Super Admin')
@section('page_title', 'Edit Jurusan')

@section('content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super.jurusan.index') }}"
                                    class="text-purple">Jurusan</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('super.jurusan.show', $jurusan->id) }}"
                                    class="text-purple">Detail</a></li>
                            <li class="breadcrumb-item active">Edit Jurusan</li>
                        </ol>
                    </nav>

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-purple-100 rounded-circle p-3 me-3">
                            <i class="bi bi-pencil-square text-purple-600 fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-0">Edit Jurusan</h5>
                            <p class="text-muted small mb-0">Ubah data jurusan</p>
                        </div>
                    </div>

                    <form id="editJurusanForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Jurusan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jurusan" class="form-control bg-light border-0 rounded-3"
                                value="{{ $jurusan->nama_jurusan }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" rows="4"
                                class="form-control bg-light border-0 rounded-3">{{ $jurusan->deskripsi }}</textarea>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-purple rounded-pill px-5">Update</button>
                            <a href="{{ route('super.jurusan.show', $jurusan->id) }}"
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
        document.getElementById('editJurusanForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            axios.post('{{ route("super.jurusan.update", $jurusan->id) }}', new FormData(this))
                .then(res => {
                    if (res.data.success) {
                        Swal.fire('Berhasil!', res.data.message, 'success').then(() => {
                            window.location.href = '{{ route("super.jurusan.show", $jurusan->id) }}';
                        });
                    } else {
                        Swal.fire('Gagal!', res.data.message, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Update';
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
                    submitBtn.innerHTML = 'Update';
                });
        });
    </script>
@endpush