@extends('layouts.public')

@section('title', 'Isi Kuesioner - SERVQUAL POLMED')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-kuesioner p-4 p-md-5 animate-fade-in-up">
                <div class="text-center mb-4">
                    <div class="icon-circle mb-3 mx-auto"
                        style="width: 70px; height: 70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-clipboard-data fs-1 text-white"></i>
                    </div>
                    <h3 class="fw-bold mt-2">Kuesioner Kepuasan Layanan</h3>
                    <p class="text-muted">Silakan berikan penilaian Anda dengan jujur (1 = Sangat Tidak Setuju, 5 = Sangat
                        Setuju)</p>

                    <!-- Informasi Periode - Lebih Menonjol -->
                    <div class="periode-card mt-3 p-3 rounded-4 shadow-sm"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-calendar-week fs-4 text-white"></i>
                                <span class="fw-semibold text-white">Periode Kuesioner:</span>
                            </div>
                            <span class=" text-purple-800 px-4 py-2 rounded-pill fw-bold shadow-sm fs-6">
                                <i class="bi bi-tag me-2"></i>{{ $periodeNama ?? 'Periode Aktif' }}
                            </span>
                            <span class="text-white-50 small">
                                <i class="bi bi-clock-history me-1"></i>{{ $periodeTanggal ?? '' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="d-flex align-items-center justify-content-between mb-5">
                    <div class="step-item text-center flex-grow-1">
                        <div class="progress-step completed mx-auto mb-2"><i class="bi bi-check-lg"></i></div>
                        <small class="text-muted">Validasi</small>
                    </div>
                    <div class="step-line mx-2"></div>
                    <div class="step-item text-center flex-grow-1">
                        <div class="progress-step active mx-auto mb-2">2</div>
                        <small class="text-muted fw-bold text-primary">Penilaian</small>
                    </div>
                    <div class="step-line mx-2"></div>
                    <div class="step-item text-center flex-grow-1">
                        <div class="progress-step mx-auto mb-2">3</div>
                        <small class="text-muted">Selesai</small>
                    </div>
                </div>

                <!-- Pilih Dosen -->
                <div class="alert alert-primary bg-light border-0 rounded-3 mb-4 d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
                    <span>Pilih dosen yang ingin Anda nilai terlebih dahulu.</span>
                </div>

                <div class="row mb-4">
                    <div class="col-md-7 mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-person-badge me-2 text-purple-600"></i>Pilih
                            Dosen</label>
                        <select id="pilihDosen" class="form-select border-0 bg-light rounded-3 shadow-sm">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosenList as $dosen)
                                <option value="{{ $dosen->id }}" data-nama="{{ $dosen->name }}" {{ $dosen->already_rated ? 'disabled' : '' }} class="{{ $dosen->already_rated ? 'text-muted' : '' }}">
                                    {{ $dosen->name }} {{ $dosen->already_rated ? '(Sudah dinilai)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-book me-2 text-purple-600"></i>Mata Kuliah
                            (opsional)</label>
                        <input type="text" id="mataKuliah" class="form-control border-0 bg-light rounded-3 shadow-sm"
                            placeholder="Nama mata kuliah">
                    </div>
                </div>

                <!-- Form Penilaian Dosen (dinamis) -->
                <div id="formPenilaianDosen" style="display: none;">
                    <hr class="my-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-star-fill text-warning fs-5"></i>
                        <h5 class="fw-bold mb-0">Penilaian untuk: <span id="namaDosenTerpilih" class="text-primary"></span>
                        </h5>
                    </div>
                    <form id="nilaiDosenForm">
                        @csrf
                        <input type="hidden" name="type" value="dosen">
                        <input type="hidden" name="dosen_id" id="dosen_id">

                        <div class="accordion" id="accordionDimensi">
                            @foreach($pertanyaanPerDimensi as $dimensi => $items)
                                <div class="accordion-item mb-3 border rounded-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed rounded-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}"
                                            style="background: #f8f9fa;">
                                            <strong>{{ $dimensi }}</strong>
                                            <span
                                                class="badge bg-purple-100 text-purple-800 rounded-pill ms-2">{{ count($items) }}
                                                pertanyaan</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionDimensi">
                                        <div class="accordion-body p-3">
                                            @foreach($items as $item)
                                                <div class="mb-4 p-3 border rounded-3 bg-white shadow-sm">
                                                    <p class="fw-semibold mb-2">{{ $loop->iteration }}. {{ $item->teks }}</p>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3 mb-md-0">
                                                            <label class="form-label small fw-semibold text-purple-800">Harapan
                                                                (Tingkat kepentingan)</label>
                                                            <select name="jawaban[{{ $item->id }}][harapan]"
                                                                class="form-select harapan-select bg-light border-0 rounded-3"
                                                                data-id="{{ $item->id }}" required>
                                                                <option value="">Pilih</option>
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}">{{ $i }} -
                                                                        {{ $i == 1 ? 'Sangat rendah' : ($i == 5 ? 'Sangat tinggi' : '') }}
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold text-purple-800">Persepsi
                                                                (Kenyataan yang Anda alami)</label>
                                                            <select name="jawaban[{{ $item->id }}][persepsi]"
                                                                class="form-select persepsi-select bg-light border-0 rounded-3"
                                                                data-id="{{ $item->id }}" required>
                                                                <option value="">Pilih</option>
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}">{{ $i }} -
                                                                        {{ $i == 1 ? 'Sangat buruk' : ($i == 5 ? 'Sangat baik' : '') }}
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="jawaban[{{ $item->id }}][id_pertanyaan]"
                                                            value="{{ $item->id }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn-purple px-5 py-2 rounded-pill shadow-sm" id="btnSimpanDosen">
                                <i class="bi bi-save me-2"></i>Simpan Penilaian Dosen
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Penilaian Fasilitas -->
                <hr class="mt-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-building fs-5 text-purple-600"></i>
                    <h5 class="fw-bold mb-0">Penilaian Fasilitas Kampus</h5>
                </div>

                @if($fasilitasSudahDiisi)
                    <div class="alert alert-success border-0 rounded-3 shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i>Anda sudah mengisi penilaian fasilitas. Terima kasih.
                    </div>
                @else
                    <form id="nilaiFasilitasForm">
                        @csrf
                        <input type="hidden" name="type" value="fasilitas">
                        <div class="accordion" id="accordionFasilitas">
                            @foreach($pertanyaanPerDimensi as $dimensi => $items)
                                <div class="accordion-item mb-3 border rounded-3 shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#fas{{ $loop->index }}" style="background: #f8f9fa;">
                                            <strong>{{ $dimensi }}</strong>
                                        </button>
                                    </h2>
                                    <div id="fas{{ $loop->index }}" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionFasilitas">
                                        <div class="accordion-body p-3">
                                            @foreach($items as $item)
                                                <div class="mb-4 p-3 border rounded-3 bg-white shadow-sm">
                                                    <p class="fw-semibold mb-2">{{ $loop->iteration }}. {{ $item->teks }}</p>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3 mb-md-0">
                                                            <label class="form-label small fw-semibold text-purple-800">Harapan</label>
                                                            <select name="jawaban_fasilitas[{{ $item->id }}][harapan]"
                                                                class="form-select bg-light border-0 rounded-3" required>
                                                                <option value="">Pilih</option>
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold text-purple-800">Persepsi</label>
                                                            <select name="jawaban_fasilitas[{{ $item->id }}][persepsi]"
                                                                class="form-select bg-light border-0 rounded-3" required>
                                                                <option value="">Pilih</option>
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="jawaban_fasilitas[{{ $item->id }}][id_pertanyaan]"
                                                            value="{{ $item->id }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn-purple px-5 py-2 rounded-pill shadow-sm" id="btnSimpanFasilitas">
                                <i class="bi bi-save me-2"></i>Simpan Penilaian Fasilitas
                            </button>
                        </div>
                    </form>
                @endif

                <div class="mt-5 text-center">
                    <div class="alert alert-light border-0 rounded-3 bg-light d-inline-block">
                        <i class="bi bi-info-circle me-2 text-purple-600"></i>
                        <small class="text-muted">Setelah selesai menilai semua dosen dan fasilitas, <a
                                href="{{ route('public.selesai') }}"
                                class="text-purple-600 fw-semibold text-decoration-none">klik di sini</a> untuk
                            mengakhiri.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Debug: cek apakah ada error di console
        console.log('Script loaded');
        console.log('Periode:', @json($periodeNama ?? 'Tidak ada'));

        const dosenSelect = document.getElementById('pilihDosen');
        const formPenilaianDosen = document.getElementById('formPenilaianDosen');
        const namaDosenSpan = document.getElementById('namaDosenTerpilih');
        const dosenIdInput = document.getElementById('dosen_id');
        const mataKuliahInput = document.getElementById('mataKuliah');

        // Event listener untuk pilih dosen
        if (dosenSelect) {
            dosenSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                if (this.value) {
                    namaDosenSpan.innerText = selectedOption.text;
                    dosenIdInput.value = this.value;
                    formPenilaianDosen.style.display = 'block';
                    formPenilaianDosen.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    formPenilaianDosen.style.display = 'none';
                }
            });
        }

        // Submit penilaian dosen
        const nilaiDosenForm = document.getElementById('nilaiDosenForm');
        if (nilaiDosenForm) {
            nilaiDosenForm.addEventListener('submit', function (e) {
                e.preventDefault();
                console.log('Submitting dosen form...');

                // Validasi: cek apakah semua select sudah dipilih
                const allHarapan = document.querySelectorAll('#nilaiDosenForm .harapan-select');
                const allPersepsi = document.querySelectorAll('#nilaiDosenForm .persepsi-select');
                let isValid = true;
                let errorMsg = '';

                allHarapan.forEach(select => {
                    if (!select.value) {
                        isValid = false;
                        errorMsg = 'Harap isi semua pilihan Harapan';
                        select.style.border = '1px solid #dc2626';
                    } else {
                        select.style.border = '';
                    }
                });

                allPersepsi.forEach(select => {
                    if (!select.value) {
                        isValid = false;
                        errorMsg = 'Harap isi semua pilihan Persepsi';
                        select.style.border = '1px solid #dc2626';
                    } else {
                        select.style.border = '';
                    }
                });

                if (!isValid) {
                    Swal.fire('Peringatan', errorMsg, 'warning');
                    return;
                }

                // Siapkan FormData
                let formData = new FormData(this);
                formData.append('mata_kuliah', mataKuliahInput ? mataKuliahInput.value : '');

                // Nonaktifkan tombol selama proses
                const submitBtn = document.getElementById('btnSimpanDosen');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                axios.post('{{ route("public.isi.simpan") }}', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(res => {
                        Swal.close();
                        if (res.data.success) {
                            Swal.fire('Berhasil', res.data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        if (err.response?.data?.errors) {
                            let msg = Object.values(err.response.data.errors).flat().join('\n');
                            Swal.fire('Validasi Gagal', msg, 'error');
                        } else if (err.response?.data?.error) {
                            Swal.fire('Error', err.response.data.error, 'error');
                        } else {
                            Swal.fire('Error', 'Terjadi kesalahan pada server. Silakan coba lagi.', 'error');
                        }
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Penilaian Dosen';
                    });
            });
        }

        // Submit penilaian fasilitas
        const nilaiFasilitasForm = document.getElementById('nilaiFasilitasForm');
        if (nilaiFasilitasForm) {
            nilaiFasilitasForm.addEventListener('submit', function (e) {
                e.preventDefault();
                console.log('Submitting fasilitas form...');

                let formData = new FormData(this);

                const submitBtn = document.getElementById('btnSimpanFasilitas');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                axios.post('{{ route("public.isi.simpan") }}', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(res => {
                        Swal.close();
                        if (res.data.success) {
                            Swal.fire('Berhasil', res.data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        if (err.response?.data?.errors) {
                            let msg = Object.values(err.response.data.errors).flat().join('\n');
                            Swal.fire('Validasi Gagal', msg, 'error');
                        } else if (err.response?.data?.error) {
                            Swal.fire('Error', err.response.data.error, 'error');
                        } else {
                            Swal.fire('Error', 'Terjadi kesalahan pada server. Silakan coba lagi.', 'error');
                        }
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Penilaian Fasilitas';
                    });
            });
        }
    </script>
@endsection