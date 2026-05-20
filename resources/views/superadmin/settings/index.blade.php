@extends('layouts.superadmin')

@section('title', 'Pengaturan Sistem - SUPER ADMIN')
@section('page_title', 'Pengaturan Sistem')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 shadow-sm p-4 p-md-5">
                    <form id="settingsForm">
                        @csrf
                        <div class="row g-4">
                            <!-- Status Kuesioner -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status Kuesioner</label>
                                <select name="kuesioner_status" class="form-select bg-light border-0 rounded-3">
                                    <option value="open" {{ ($settings['kuesioner_status'] ?? 'closed') == 'open' ? 'selected' : '' }}>Terbuka (Open)</option>
                                    <option value="closed" {{ ($settings['kuesioner_status'] ?? 'closed') == 'closed' ? 'selected' : '' }}>Tertutup (Closed)</option>
                                </select>
                                <div class="form-text">Hanya berpengaruh jika tidak menggunakan manajemen periode.</div>
                            </div>

                            <!-- Target Jurusan -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Jurusan</label>
                                <input type="text" name="target_jurusan" class="form-control bg-light border-0 rounded-3"
                                    value="{{ $settings['target_jurusan'] ?? '' }}"
                                    placeholder="Contoh: teknologi_informasi">
                                <div class="form-text">Kosongkan atau isi "all" untuk semua jurusan.</div>
                            </div>

                            <!-- Tujuan Kuesioner -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tujuan Kuesioner</label>
                                <textarea name="tujuan_kuesioner" rows="3"
                                    class="form-control bg-light border-0 rounded-3">{{ $settings['tujuan_kuesioner'] ?? '' }}</textarea>
                            </div>

                            <!-- Nama & Versi Aplikasi -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Aplikasi</label>
                                <input type="text" name="app_name" class="form-control bg-light border-0 rounded-3"
                                    value="{{ $settings['app_name'] ?? 'Sistem Monitoring SERVQUAL' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Versi Aplikasi</label>
                                <input type="text" name="app_version" class="form-control bg-light border-0 rounded-3"
                                    value="{{ $settings['app_version'] ?? '1.0.0' }}">
                            </div>
                        </div>

                        <div class="mt-5 d-flex justify-content-end">
                            <button type="submit" class="btn btn-purple rounded-pill px-5 py-2">
                                <i class="bi bi-save me-2"></i>Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('settingsForm');
            if (!form) return;

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Pengaturan sedang disimpan',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                axios.post('{{ route("super.settings.update") }}', data)
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire('Berhasil', response.data.message, 'success');
                        } else {
                            Swal.fire('Gagal', response.data.message, 'error');
                        }
                    })
                    .catch(error => {
                        if (error.response && error.response.data.errors) {
                            const errors = error.response.data.errors;
                            let errorMsg = '';
                            for (let key in errors) {
                                errorMsg += errors[key][0] + '\n';
                            }
                            Swal.fire('Validasi Gagal', errorMsg, 'error');
                        } else {
                            Swal.fire('Error', 'Terjadi kesalahan', 'error');
                        }
                    });
            });
        })();
    </script>
@endpush