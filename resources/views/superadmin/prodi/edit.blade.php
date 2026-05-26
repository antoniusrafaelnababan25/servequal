@extends('layouts.superadmin')

@section('title', 'Edit Program Studi - Super Admin')
@section('page_title', 'Edit Program Studi')

@section('content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super.prodi.index') }}"
                                    class="text-purple">Program Studi</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('super.prodi.show', $prodi->id) }}"
                                    class="text-purple">Detail</a></li>
                            <li class="breadcrumb-item active">Edit Program Studi</li>
                        </ol>
                    </nav>

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-purple-100 rounded-circle p-3 me-3">
                            <i class="bi bi-pencil-square text-purple-600 fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-0">Edit Program Studi</h5>
                            <p class="text-muted small mb-0">Ubah data program studi</p>
                        </div>
                    </div>

                    <form id="editProdiForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jurusan <span class="text-danger">*</span></label>
                            <select name="jurusan_id" class="form-select bg-light border-0 rounded-3" required>
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->id }}" {{ $prodi->jurusan_id == $jurusan->id ? 'selected' : '' }}>
                                        {{ $jurusan->nama_jurusan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Program Studi <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_prodi" class="form-control bg-light border-0 rounded-3"
                                value="{{ $prodi->nama_prodi }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenjang <span class="text-danger">*</span></label>
                            <select name="jenjang" class="form-select bg-light border-0 rounded-3" required>
                                <option value="">Pilih Jenjang</option>
                                <option value="sarjana" {{ $prodi->jenjang == 'sarjana' ? 'selected' : '' }}>Sarjana (S1)
                                </option>
                                <option value="pascasarjana" {{ $prodi->jenjang == 'pascasarjana' ? 'selected' : '' }}>
                                    Pascasarjana (S2)</option>
                                <option value="internasional" {{ $prodi->jenjang == 'internasional' ? 'selected' : '' }}>
                                    Internasional</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1" {{ $prodi->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    <i class="bi bi-check-circle-fill me-1 text-success"></i>Aktifkan Program Studi
                                </label>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-purple rounded-pill px-5">Update</button>
                            <a href="{{ route('super.prodi.show', $prodi->id) }}"
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
        document.getElementById('editProdiForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            axios.post('{{ route("super.prodi.update", $prodi->id) }}', new FormData(this))
                .then(res => {
                    if (res.data.success) {
                        Swal.fire('Berhasil!', res.data.message, 'success').then(() => {
                            window.location.href = '{{ route("super.prodi.show", $prodi->id) }}';
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