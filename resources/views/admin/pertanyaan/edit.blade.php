@extends('layouts.admin')

@section('title', 'Edit Pertanyaan - Admin')
@section('page_title', 'Edit Pertanyaan')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
                    <form id="editPertanyaanForm">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Dimensi SERVQUAL <span
                                        class="text-danger">*</span></label>
                                <select name="dimensi" class="form-select bg-light border-0 rounded-3" required>
                                    @foreach($dimensiList as $dim)
                                        <option value="{{ $dim }}" {{ $pertanyaan->dimensi == $dim ? 'selected' : '' }}>{{ $dim }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Role <span class="text-danger">*</span></label>
                                <select name="target_role" class="form-select bg-light border-0 rounded-3" required>
                                    <option value="mahasiswa" {{ $pertanyaan->target_role == 'mahasiswa' ? 'selected' : '' }}>
                                        Mahasiswa</option>
                                    <option value="dosen" {{ $pertanyaan->target_role == 'dosen' ? 'selected' : '' }}>Dosen
                                    </option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Teks Pertanyaan <span
                                        class="text-danger">*</span></label>
                                <textarea name="teks" rows="4" class="form-control bg-light border-0 rounded-3"
                                    required>{{ $pertanyaan->teks }}</textarea>
                            </div>
                        </div>
                        <div class="mt-5 d-flex gap-3 justify-content-end">
                            <a href="{{ route('admin.pertanyaan.index') }}"
                                class="btn btn-secondary rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-purple rounded-pill px-4">Update Pertanyaan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('editPertanyaanForm').addEventListener('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            let pertanyaanId = {{ $pertanyaan->id }};
            axios.post('/admin/pertanyaan/' + pertanyaanId, formData)
                .then(response => {
                    if (response.data.success) Swal.fire('Berhasil', response.data.message, 'success').then(() => window.location.href = '{{ route("admin.pertanyaan.index") }}');
                })
                .catch(error => {
                    if (error.response && error.response.data.errors) {
                        let msg = Object.values(error.response.data.errors).flat().join('\n');
                        Swal.fire('Validasi Gagal', msg, 'error');
                    } else Swal.fire('Error', 'Terjadi kesalahan', 'error');
                });
        });
    </script>
@endpush