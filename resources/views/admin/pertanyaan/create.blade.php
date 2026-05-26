@extends('layouts.admin')

@section('title', 'Tambah Pertanyaan - Admin')
@section('page_title', 'Tambah Pertanyaan')

@section('content')
    <div class="container-fluid px-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.pertanyaan.index') }}" class="text-purple text-decoration-none">
                                    <i class="bi bi-question-circle me-1"></i>Pertanyaan
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Tambah Pertanyaan</li>
                        </ol>
                    </nav>

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-purple bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="bi bi-plus-circle-fill text-purple-600 fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-0">Tambah Pertanyaan Baru</h5>
                            <p class="text-muted small mb-0">Isi form di bawah untuk menambahkan pertanyaan</p>
                        </div>
                    </div>

                    <form id="questionForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-tag me-1 text-purple-600"></i>Tipe Penilaian <span
                                        class="text-danger">*</span>
                                </label>
                                <select name="tipe_penilaian" id="tipe_penilaian"
                                    class="form-select bg-light border-0 rounded-3" required>
                                    <option value="">Pilih Tipe Penilaian</option>
                                    @foreach($tipePenilaianList as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-diagram-3 me-1 text-purple-600"></i>Dimensi <span
                                        class="text-danger">*</span>
                                </label>
                                <select name="dimensi" id="dimensi" class="form-select bg-light border-0 rounded-3"
                                    required>
                                    <option value="">Pilih Dimensi</option>
                                    @foreach($dimensiList as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-1 text-purple-600"></i>Target Responden <span
                                        class="text-danger">*</span>
                                </label>
                                <select name="target_role" id="target_role" class="form-select bg-light border-0 rounded-3"
                                    required>
                                    <option value="">Pilih Target Responden</option>
                                    @foreach($targetRoleList as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">Mahasiswa</span> hanya untuk
                                    mahasiswa,
                                    <span class="badge bg-info bg-opacity-10 text-info">Dosen</span> hanya untuk dosen,
                                    <span class="badge bg-success bg-opacity-10 text-success">Both</span> untuk keduanya
                                </div>
                            </div>

                            <div class="col-md-6 mb-3" id="kategori_fasilitas_container" style="display: none;">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-building me-1 text-purple-600"></i>Kategori Fasilitas <span
                                        class="text-danger">*</span>
                                </label>
                                <select name="kategori_fasilitas" id="kategori_fasilitas"
                                    class="form-select bg-light border-0 rounded-3">
                                    <option value="">Pilih Kategori Fasilitas</option>
                                    @foreach($kategoriFasilitasList as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Khusus untuk tipe penilaian fasilitas</div>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-chat-text me-1 text-purple-600"></i>Teks Pertanyaan <span
                                        class="text-danger">*</span>
                                </label>
                                <textarea name="teks" id="teks" rows="4" class="form-control bg-light border-0 rounded-3"
                                    placeholder="Masukkan teks pertanyaan..." required></textarea>
                                <div class="form-text" id="charCount">Minimal 5, maksimal 500 karakter</div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-purple rounded-pill px-5 py-2" id="submitBtn">
                                <i class="bi bi-save me-2"></i>Simpan Pertanyaan
                            </button>
                            <a href="{{ route('admin.pertanyaan.index') }}"
                                class="btn btn-outline-secondary rounded-pill px-4 py-2">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .text-purple-600 {
            color: #4c1d95;
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
        document.addEventListener('DOMContentLoaded', function () {
            const teksInput = document.getElementById('teks');
            const charCount = document.getElementById('charCount');
            const tipePenilaian = document.getElementById('tipe_penilaian');
            const kategoriContainer = document.getElementById('kategori_fasilitas_container');
            const kategoriSelect = document.getElementById('kategori_fasilitas');

            function updateCharCount() {
                let length = teksInput.value.length;
                charCount.innerHTML = `Karakter: ${length} / 500 (minimal 5)`;
                if (length < 5 || length > 500) {
                    charCount.classList.add('text-danger');
                    charCount.classList.remove('text-success');
                } else {
                    charCount.classList.remove('text-danger');
                    charCount.classList.add('text-success');
                }
            }

            teksInput.addEventListener('input', updateCharCount);
            updateCharCount();

            tipePenilaian.addEventListener('change', function () {
                if (this.value === 'penilaian_fasilitas') {
                    kategoriContainer.style.display = 'block';
                    kategoriSelect.required = true;
                } else {
                    kategoriContainer.style.display = 'none';
                    kategoriSelect.required = false;
                    kategoriSelect.value = '';
                }
            });

            document.getElementById('questionForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

                Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                axios.post('{{ route("admin.pertanyaan.store") }}', new FormData(this), {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                })
                    .then(res => {
                        Swal.close();
                        if (res.data.success) {
                            Swal.fire('Berhasil!', res.data.message, 'success').then(() => {
                                window.location.href = '{{ route("admin.pertanyaan.index") }}';
                            });
                        } else {
                            Swal.fire('Gagal!', res.data.message, 'error');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Pertanyaan';
                        }
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
                        submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Pertanyaan';
                    });
            });
        });
    </script>
@endpush