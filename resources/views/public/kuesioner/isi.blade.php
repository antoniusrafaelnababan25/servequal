@extends('layouts.public')

@section('title', 'Isi Kuesioner - SERVQUAL POLMED')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 rounded-4 shadow-lg overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <!-- Skala Penilaian -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card border-0 bg-danger bg-opacity-10 h-100">
                                    <div class="card-body text-center py-3">
                                        <i class="bi bi-emoji-frown fs-2 text-danger"></i>
                                        <h6 class="fw-bold mt-2 mb-0">1 - Sangat Tidak Setuju</h6>
                                        <small class="text-muted">Sangat Buruk</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-warning bg-opacity-10 h-100">
                                    <div class="card-body text-center py-3">
                                        <i class="bi bi-emoji-neutral fs-2 text-warning"></i>
                                        <h6 class="fw-bold mt-2 mb-0">3 - Netral</h6>
                                        <small class="text-muted">Cukup</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-success bg-opacity-10 h-100">
                                    <div class="card-body text-center py-3">
                                        <i class="bi bi-emoji-smile fs-2 text-success"></i>
                                        <h6 class="fw-bold mt-2 mb-0">5 - Sangat Setuju</h6>
                                        <small class="text-muted">Sangat Baik</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Periode -->
                        <div class="alert alert-info border-0 rounded-4 mb-4 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-week fs-5"></i>
                                    <span class="fw-semibold">Periode Aktif:</span>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                    <i class="bi bi-tag me-1"></i>{{ $periodeNama ?? 'Periode Aktif' }}
                                </span>
                                <span class="text-muted small">
                                    <i class="bi bi-clock-history me-1"></i>
                                    {{ $periodeMulai ?? '' }} - {{ $periodeSelesai ?? '' }}
                                </span>
                            </div>
                        </div>

                        <!-- Progress Steps -->
                        <div class="d-flex align-items-center justify-content-between mb-5">
                            <div class="step-item text-center flex-grow-1">
                                <div class="step-circle completed mx-auto mb-2">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <small class="text-muted">Validasi</small>
                            </div>
                            <div class="step-line flex-grow-1 mx-2"></div>
                            <div class="step-item text-center flex-grow-1">
                                <div class="step-circle active mx-auto mb-2">
                                    <span class="fw-bold">2</span>
                                </div>
                                <small class="fw-bold text-primary">Penilaian</small>
                            </div>
                            <div class="step-line flex-grow-1 mx-2"></div>
                            <div class="step-item text-center flex-grow-1">
                                <div class="step-circle mx-auto mb-2">
                                    <span>3</span>
                                </div>
                                <small class="text-muted">Selesai</small>
                            </div>
                        </div>

                        <!-- Progress Penilaian Dosen -->
                        <div class="card bg-light border-0 rounded-4 mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold">
                                        <i class="bi bi-person-badge me-2 text-primary"></i>
                                        Progress Penilaian Dosen
                                    </span>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $jumlahDosenDinilai ?? 0 }}/{{ $totalDosen ?? 0 }} Dosen
                                    </span>
                                </div>
                                <div class="progress" style="height: 12px; border-radius: 10px;">
                                    <div class="progress-bar bg-gradient-primary" role="progressbar"
                                        style="width: {{ $progressDosen ?? 0 }}%; border-radius: 10px;"
                                        aria-valuenow="{{ $progressDosen ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="text-end mt-1">
                                    <small class="text-muted">{{ $progressDosen ?? 0 }}% Selesai</small>
                                </div>
                            </div>
                        </div>

                        <!-- Pilih Dosen -->
                        <div class="alert alert-light border-start border-4 border-primary rounded-3 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
                                <span>Pilih dosen yang ingin Anda nilai terlebih dahulu. Dosen yang sudah dinilai akan
                                    terkunci.</span>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person-badge me-2 text-primary"></i>Pilih Dosen
                                </label>
                                <select id="pilihDosen"
                                    class="form-select form-select-lg border-0 bg-light rounded-3 shadow-sm">
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach($dosenList as $dosen)
                                        <option value="{{ $dosen->id }}" data-nama="{{ $dosen->name }}"
                                            data-nidn="{{ $dosen->nidn }}" {{ $dosen->already_rated ? 'disabled' : '' }}
                                            class="{{ $dosen->already_rated ? 'text-muted' : '' }}">
                                            {{ $dosen->name }}
                                            {{ $dosen->nidn ? '(' . $dosen->nidn . ')' : '' }}
                                            {!! $dosen->already_rated ? '✅ Sudah dinilai' : '' !!}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-book me-2 text-primary"></i>Mata Kuliah <small
                                        class="text-muted">(opsional)</small>
                                </label>
                                <input type="text" id="mataKuliah"
                                    class="form-control form-control-lg border-0 bg-light rounded-3 shadow-sm"
                                    placeholder="Nama mata kuliah">
                            </div>
                        </div>

                        <!-- Form Penilaian Dosen -->
                        <div id="formPenilaianDosen" style="display: none;">
                            <div class="card bg-light border-0 rounded-4 mb-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                            <i class="bi bi-star-fill text-warning fs-5"></i>
                                        </div>
                                        <h5 class="fw-bold mb-0">Penilaian untuk: <span id="namaDosenTerpilih"
                                                class="text-primary"></span></h5>
                                    </div>
                                    <div id="dosenInfo" class="small text-muted mt-2"></div>
                                    <div class="alert alert-info mt-2 small">
                                        <i class="bi bi-info-circle me-1"></i>
                                        <strong>Catatan:</strong> Anda dapat mengisi pertanyaan secara bertahap.
                                        Minimal 1 pertanyaan yang diisi lengkap (Harapan dan Persepsi) untuk dapat
                                        menyimpan.
                                    </div>
                                </div>
                            </div>

                            <form id="nilaiDosenForm">
                                @csrf
                                <input type="hidden" name="type" value="dosen">
                                <input type="hidden" name="dosen_id" id="dosen_id">
                                <input type="hidden" name="mata_kuliah" id="mata_kuliah_hidden">

                                <div class="accordion" id="accordionDimensi">
                                    @foreach($pertanyaanDosenPerDimensi as $dimensi => $items)
                                        <div class="accordion-item mb-3 border-0 rounded-4 shadow-sm overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed bg-light" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseDosen{{ $loop->index }}">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                            <i class="bi bi-diagram-3 text-primary"></i>
                                                        </div>
                                                        <strong>{{ $dimensi }}</strong>
                                                        <span class="badge bg-primary rounded-pill ms-2">{{ count($items) }}
                                                            pertanyaan</span>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseDosen{{ $loop->index }}" class="accordion-collapse collapse"
                                                data-bs-parent="#accordionDimensi">
                                                <div class="accordion-body p-3">
                                                    @foreach($items as $idx => $item)
                                                        <div class="question-card mb-3 p-3 bg-white rounded-3 shadow-sm border"
                                                            id="question-{{ $item->id }}">
                                                            <p class="fw-semibold mb-3">{{ $loop->iteration }}. {{ $item->teks }}
                                                            </p>
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-semibold text-primary">
                                                                        <i class="bi bi-flag me-1"></i>Harapan (Tingkat kepentingan)
                                                                    </label>
                                                                    <div class="rating-stars-harapan mb-2"
                                                                        data-question-id="{{ $item->id }}" data-type="harapan">
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <i class="bi bi-star fs-3 text-secondary"
                                                                                data-value="{{ $i }}" style="cursor: pointer;"></i>
                                                                        @endfor
                                                                    </div>
                                                                    <select name="jawaban[{{ $item->id }}][harapan]"
                                                                        class="form-select harapan-select-{{ $item->id }} bg-light border-0 rounded-3 d-none"
                                                                        data-id="{{ $item->id }}">
                                                                        <option value="">Pilih</option>
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <option value="{{ $i }}">{{ $i }} -
                                                                                @if($i == 1) Sangat Rendah
                                                                                @elseif($i == 2) Rendah
                                                                                @elseif($i == 3) Cukup
                                                                                @elseif($i == 4) Tinggi
                                                                                @else Sangat Tinggi @endif
                                                                            </option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label small fw-semibold text-primary">
                                                                        <i class="bi bi-eye me-1"></i>Persepsi (Kenyataan yang
                                                                        dialami)
                                                                    </label>
                                                                    <div class="rating-stars-persepsi mb-2"
                                                                        data-question-id="{{ $item->id }}" data-type="persepsi">
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <i class="bi bi-star fs-3 text-secondary"
                                                                                data-value="{{ $i }}" style="cursor: pointer;"></i>
                                                                        @endfor
                                                                    </div>
                                                                    <select name="jawaban[{{ $item->id }}][persepsi]"
                                                                        class="form-select persepsi-select-{{ $item->id }} bg-light border-0 rounded-3 d-none"
                                                                        data-id="{{ $item->id }}">
                                                                        <option value="">Pilih</option>
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <option value="{{ $i }}">{{ $i }} -
                                                                                @if($i == 1) Sangat Buruk
                                                                                @elseif($i == 2) Buruk
                                                                                @elseif($i == 3) Cukup
                                                                                @elseif($i == 4) Baik
                                                                                @else Sangat Baik @endif
                                                                            </option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                                <input type="hidden" name="jawaban[{{ $item->id }}][id_pertanyaan]"
                                                                    value="{{ $item->id }}">
                                                            </div>
                                                            <div class="small text-muted mt-2" id="status-{{ $item->id }}">
                                                                <i class="bi bi-clock"></i> Belum diisi
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Informasi:</strong> Anda tidak perlu mengisi semua pertanyaan.
                                    Minimal 1 pertanyaan dengan Harapan dan Persepsi yang diisi untuk dapat menyimpan.
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm"
                                        id="btnSimpanDosen">
                                        <i class="bi bi-save me-2"></i>Simpan Penilaian Dosen
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Penilaian Fasilitas -->
                        <div class="mt-5 pt-3">
                            <div class="d-flex align-items-center gap-2 mb-4">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                    <i class="bi bi-building fs-4 text-success"></i>
                                </div>
                                <h5 class="fw-bold mb-0">Penilaian Fasilitas Kampus</h5>
                            </div>

                            @if($fasilitasSudahDiisi)
                                <div class="alert alert-success border-0 rounded-4 shadow-sm">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill fs-4"></i>
                                        <div>
                                            <strong>Terima kasih!</strong><br>
                                            <span>Anda sudah mengisi penilaian fasilitas.</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Catatan:</strong> Anda dapat mengisi pertanyaan fasilitas secara bertahap.
                                    Minimal 1 pertanyaan yang diisi lengkap (Harapan dan Persepsi) untuk dapat menyimpan.
                                </div>

                                <form id="nilaiFasilitasForm">
                                    @csrf
                                    <input type="hidden" name="type" value="fasilitas">

                                    @foreach($pertanyaanFasilitasPerKategori as $kategori => $items)
                                        <div class="card mb-4 border-0 rounded-4 shadow-sm overflow-hidden">
                                            <div class="card-header bg-success bg-opacity-10 border-0 py-3">
                                                <h6 class="fw-bold mb-0 text-success">
                                                    <i class="bi bi-building me-2"></i>
                                                    {{ ucfirst($kategori) }}
                                                    <span class="badge bg-success rounded-pill ms-2">{{ count($items) }}
                                                        pertanyaan</span>
                                                </h6>
                                            </div>
                                            <div class="card-body p-3">
                                                @foreach($items as $idx => $item)
                                                    <div class="question-card mb-3 p-3 bg-white rounded-3 shadow-sm border"
                                                        id="fasilitas-question-{{ $item->id }}">
                                                        <p class="fw-semibold mb-3">{{ $loop->iteration }}. {{ $item->teks }}</p>
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold text-success">
                                                                    <i class="bi bi-flag me-1"></i>Harapan
                                                                </label>
                                                                <div class="rating-stars-fasilitas-harapan mb-2"
                                                                    data-question-id="{{ $item->id }}" data-type="harapan">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <i class="bi bi-star fs-3 text-secondary" data-value="{{ $i }}"
                                                                            style="cursor: pointer;"></i>
                                                                    @endfor
                                                                </div>
                                                                <select name="jawaban_fasilitas[{{ $item->id }}][harapan]"
                                                                    class="form-select fasilitas-harapan-select-{{ $item->id }} bg-light border-0 rounded-3 d-none">
                                                                    <option value="">Pilih</option>
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold text-success">
                                                                    <i class="bi bi-eye me-1"></i>Persepsi
                                                                </label>
                                                                <div class="rating-stars-fasilitas-persepsi mb-2"
                                                                    data-question-id="{{ $item->id }}" data-type="persepsi">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <i class="bi bi-star fs-3 text-secondary" data-value="{{ $i }}"
                                                                            style="cursor: pointer;"></i>
                                                                    @endfor
                                                                </div>
                                                                <select name="jawaban_fasilitas[{{ $item->id }}][persepsi]"
                                                                    class="form-select fasilitas-persepsi-select-{{ $item->id }} bg-light border-0 rounded-3 d-none">
                                                                    <option value="">Pilih</option>
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                            <input type="hidden"
                                                                name="jawaban_fasilitas[{{ $item->id }}][id_pertanyaan]"
                                                                value="{{ $item->id }}">
                                                        </div>
                                                        <div class="small text-muted mt-2" id="fasilitas-status-{{ $item->id }}">
                                                            <i class="bi bi-clock"></i> Belum diisi
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="alert alert-warning mt-3">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <strong>Informasi:</strong> Anda tidak perlu mengisi semua pertanyaan.
                                        Minimal 1 pertanyaan dengan Harapan dan Persepsi yang diisi untuk dapat menyimpan.
                                    </div>

                                    <div class="mt-4 text-end">
                                        <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill shadow-sm"
                                            id="btnSimpanFasilitas">
                                            <i class="bi bi-save me-2"></i>Simpan Penilaian Fasilitas
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>

                        <!-- Link Selesai -->
                        <div class="mt-5 pt-3 text-center">
                            <div class="card bg-light border-0 rounded-4">
                                <div class="card-body">
                                    <i class="bi bi-info-circle text-primary fs-4 mb-2 d-block"></i>
                                    <small class="text-muted">
                                        Setelah selesai menilai semua dosen dan fasilitas,
                                        <a href="{{ route('public.selesai') }}"
                                            class="text-primary fw-semibold text-decoration-none">
                                            klik di sini <i class="bi bi-box-arrow-right"></i>
                                        </a> untuk mengakhiri.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .progress-bar.bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .step-circle {
            width: 45px;
            height: 45px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .step-circle.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .step-circle.completed {
            background-color: #28a745;
            color: white;
        }

        .step-line {
            height: 2px;
            background: #e9ecef;
            flex-grow: 1;
        }

        .rating-stars-harapan i,
        .rating-stars-persepsi i,
        .rating-stars-fasilitas-harapan i,
        .rating-stars-fasilitas-persepsi i {
            transition: all 0.2s ease;
            cursor: pointer;
            margin-right: 5px;
        }

        .rating-stars-harapan i:hover,
        .rating-stars-persepsi i:hover,
        .rating-stars-fasilitas-harapan i:hover,
        .rating-stars-fasilitas-persepsi i:hover {
            transform: scale(1.2);
            color: #ffc107 !important;
        }

        .rating-stars-harapan i.active,
        .rating-stars-persepsi i.active,
        .rating-stars-fasilitas-harapan i.active,
        .rating-stars-fasilitas-persepsi i.active {
            color: #ffc107 !important;
            text-shadow: 0 0 8px rgba(255, 193, 7, 0.5);
        }

        .question-card {
            transition: all 0.3s ease;
        }

        .question-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }

        @media (max-width: 768px) {
            .step-circle {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }

            .rating-stars-harapan i,
            .rating-stars-persepsi i,
            .rating-stars-fasilitas-harapan i,
            .rating-stars-fasilitas-persepsi i {
                font-size: 20px !important;
            }

            .btn-lg {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dosenSelect = document.getElementById('pilihDosen');
            const formPenilaianDosen = document.getElementById('formPenilaianDosen');
            const namaDosenSpan = document.getElementById('namaDosenTerpilih');
            const dosenIdInput = document.getElementById('dosen_id');
            const mataKuliahInput = document.getElementById('mataKuliah');
            const mataKuliahHidden = document.getElementById('mata_kuliah_hidden');
            const dosenInfo = document.getElementById('dosenInfo');

            // Fungsi untuk update status pertanyaan
            function updateQuestionStatus(questionId, type) {
                const harapanSelect = document.querySelector(`.harapan-select-${questionId}`);
                const persepsiSelect = document.querySelector(`.persepsi-select-${questionId}`);
                const statusSpan = document.getElementById(`status-${questionId}`);

                if (harapanSelect && persepsiSelect && statusSpan) {
                    const harapanFilled = harapanSelect.value && harapanSelect.value !== '';
                    const persepsiFilled = persepsiSelect.value && persepsiSelect.value !== '';

                    if (harapanFilled && persepsiFilled) {
                        statusSpan.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> Lengkap';
                        statusSpan.classList.add('text-success');
                        statusSpan.classList.remove('text-muted');
                    } else if (harapanFilled || persepsiFilled) {
                        statusSpan.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-warning"></i> Belum lengkap (isi keduanya)';
                        statusSpan.classList.add('text-warning');
                        statusSpan.classList.remove('text-muted');
                    } else {
                        statusSpan.innerHTML = '<i class="bi bi-clock"></i> Belum diisi';
                        statusSpan.classList.remove('text-success', 'text-warning');
                        statusSpan.classList.add('text-muted');
                    }
                }
            }

            // Fungsi untuk update status pertanyaan fasilitas
            function updateFasilitasStatus(questionId) {
                const harapanSelect = document.querySelector(`.fasilitas-harapan-select-${questionId}`);
                const persepsiSelect = document.querySelector(`.fasilitas-persepsi-select-${questionId}`);
                const statusSpan = document.getElementById(`fasilitas-status-${questionId}`);

                if (harapanSelect && persepsiSelect && statusSpan) {
                    const harapanFilled = harapanSelect.value && harapanSelect.value !== '';
                    const persepsiFilled = persepsiSelect.value && persepsiSelect.value !== '';

                    if (harapanFilled && persepsiFilled) {
                        statusSpan.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> Lengkap';
                        statusSpan.classList.add('text-success');
                        statusSpan.classList.remove('text-muted');
                    } else if (harapanFilled || persepsiFilled) {
                        statusSpan.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-warning"></i> Belum lengkap (isi keduanya)';
                        statusSpan.classList.add('text-warning');
                        statusSpan.classList.remove('text-muted');
                    } else {
                        statusSpan.innerHTML = '<i class="bi bi-clock"></i> Belum diisi';
                        statusSpan.classList.remove('text-success', 'text-warning');
                        statusSpan.classList.add('text-muted');
                    }
                }
            }

            // Inisialisasi rating stars untuk dosen
            function initRatingStarsForContainer(containerSelector, isFasilitas = false) {
                const containers = document.querySelectorAll(containerSelector);
                containers.forEach(container => {
                    const stars = container.querySelectorAll('i');
                    const questionId = container.dataset.questionId;
                    let selectElement = null;

                    if (container.classList.contains('rating-stars-harapan')) {
                        selectElement = document.querySelector(`.harapan-select-${questionId}`);
                    } else if (container.classList.contains('rating-stars-persepsi')) {
                        selectElement = document.querySelector(`.persepsi-select-${questionId}`);
                    } else if (container.classList.contains('rating-stars-fasilitas-harapan')) {
                        selectElement = document.querySelector(`.fasilitas-harapan-select-${questionId}`);
                    } else if (container.classList.contains('rating-stars-fasilitas-persepsi')) {
                        selectElement = document.querySelector(`.fasilitas-persepsi-select-${questionId}`);
                    }

                    if (!selectElement) return;

                    stars.forEach(star => {
                        const newStar = star.cloneNode(true);
                        star.parentNode.replaceChild(newStar, star);
                    });

                    const updatedStars = container.querySelectorAll('i');
                    updatedStars.forEach(star => {
                        star.addEventListener('click', function (e) {
                            e.stopPropagation();
                            const value = parseInt(this.dataset.value);
                            selectElement.value = value;
                            selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                            updatedStars.forEach((s, idx) => {
                                if (idx < value) {
                                    s.classList.add('active');
                                    s.classList.remove('text-secondary');
                                } else {
                                    s.classList.remove('active');
                                    s.classList.add('text-secondary');
                                }
                            });

                            // Update status
                            if (isFasilitas) {
                                updateFasilitasStatus(questionId);
                            } else {
                                updateQuestionStatus(questionId);
                            }
                        });
                    });

                    if (selectElement.value) {
                        const value = parseInt(selectElement.value);
                        updatedStars.forEach((s, idx) => {
                            if (idx < value) {
                                s.classList.add('active');
                                s.classList.remove('text-secondary');
                            } else {
                                s.classList.remove('active');
                                s.classList.add('text-secondary');
                            }
                        });
                    }

                    // Add change listener to select
                    selectElement.addEventListener('change', function () {
                        if (isFasilitas) {
                            updateFasilitasStatus(questionId);
                        } else {
                            updateQuestionStatus(questionId);
                        }
                    });
                });
            }

            function initializeAllRatingStars() {
                initRatingStarsForContainer('.rating-stars-harapan', false);
                initRatingStarsForContainer('.rating-stars-persepsi', false);
                initRatingStarsForContainer('.rating-stars-fasilitas-harapan', true);
                initRatingStarsForContainer('.rating-stars-fasilitas-persepsi', true);
            }

            if (dosenSelect) {
                dosenSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    if (this.value) {
                        namaDosenSpan.innerText = selectedOption.text.replace(/✅.*$/, '').trim();
                        dosenIdInput.value = this.value;
                        if (selectedOption.dataset.nidn) {
                            dosenInfo.innerHTML = `<i class="bi bi-card-text me-1"></i>NIDN: ${selectedOption.dataset.nidn}`;
                        } else {
                            dosenInfo.innerHTML = '';
                        }
                        formPenilaianDosen.style.display = 'block';
                        formPenilaianDosen.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        setTimeout(() => initializeAllRatingStars(), 100);
                    } else {
                        formPenilaianDosen.style.display = 'none';
                    }
                });
            }

            if (mataKuliahInput && mataKuliahHidden) {
                mataKuliahInput.addEventListener('input', function () {
                    mataKuliahHidden.value = this.value;
                });
            }

            // Submit Penilaian Dosen
            document.getElementById('nilaiDosenForm')?.addEventListener('submit', function (e) {
                e.preventDefault();
                if (mataKuliahHidden && mataKuliahInput) mataKuliahHidden.value = mataKuliahInput.value;

                // Cek apakah ada minimal satu pertanyaan yang lengkap (harapan dan persepsi terisi)
                let hasComplete = false;
                document.querySelectorAll('[class*="harapan-select-"]').forEach(select => {
                    const questionId = select.className.match(/harapan-select-(\d+)/)?.[1];
                    if (questionId) {
                        const harapanVal = select.value;
                        const persepsiSelect = document.querySelector(`.persepsi-select-${questionId}`);
                        const persepsiVal = persepsiSelect?.value;
                        if (harapanVal && persepsiVal && harapanVal !== '' && persepsiVal !== '') {
                            hasComplete = true;
                        }
                    }
                });

                if (!hasComplete) {
                    Swal.fire('Peringatan', 'Harap isi minimal satu pertanyaan dengan lengkap (Harapan dan Persepsi harus diisi)', 'warning');
                    return;
                }

                const submitBtn = document.getElementById('btnSimpanDosen');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

                Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                axios.post('{{ route("public.isi.simpan") }}', new FormData(this), {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                })
                    .then(res => {
                        Swal.close();
                        if (res.data.success) {
                            Swal.fire('Berhasil', res.data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', res.data.message || 'Terjadi kesalahan', 'error');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Penilaian Dosen';
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        let errorMessage = err.response?.data?.message || 'Terjadi kesalahan pada server';
                        if (err.response?.data?.errors) {
                            errorMessage = Object.values(err.response.data.errors).flat().join('\n');
                        }
                        Swal.fire('Error', errorMessage, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Penilaian Dosen';
                    });
            });

            // Submit Penilaian Fasilitas
            document.getElementById('nilaiFasilitasForm')?.addEventListener('submit', function (e) {
                e.preventDefault();

                // Cek apakah ada minimal satu pertanyaan yang lengkap
                let hasComplete = false;
                document.querySelectorAll('[class*="fasilitas-harapan-select-"]').forEach(select => {
                    const questionId = select.className.match(/fasilitas-harapan-select-(\d+)/)?.[1];
                    if (questionId) {
                        const harapanVal = select.value;
                        const persepsiSelect = document.querySelector(`.fasilitas-persepsi-select-${questionId}`);
                        const persepsiVal = persepsiSelect?.value;
                        if (harapanVal && persepsiVal && harapanVal !== '' && persepsiVal !== '') {
                            hasComplete = true;
                        }
                    }
                });

                if (!hasComplete) {
                    Swal.fire('Peringatan', 'Harap isi minimal satu pertanyaan fasilitas dengan lengkap (Harapan dan Persepsi harus diisi)', 'warning');
                    return;
                }

                const submitBtn = document.getElementById('btnSimpanFasilitas');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

                Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                axios.post('{{ route("public.isi.simpan") }}', new FormData(this), {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                })
                    .then(res => {
                        Swal.close();
                        if (res.data.success) {
                            Swal.fire('Berhasil', res.data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', res.data.message || 'Terjadi kesalahan', 'error');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Penilaian Fasilitas';
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        let errorMessage = err.response?.data?.message || 'Terjadi kesalahan pada server';
                        if (err.response?.data?.errors) {
                            errorMessage = Object.values(err.response.data.errors).flat().join('\n');
                        }
                        Swal.fire('Error', errorMessage, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>Simpan Penilaian Fasilitas';
                    });
            });

            initializeAllRatingStars();

            document.querySelectorAll('.accordion-button').forEach(btn => {
                btn.addEventListener('click', () => setTimeout(() => initializeAllRatingStars(), 200));
            });
        });
    </script>
@endsection