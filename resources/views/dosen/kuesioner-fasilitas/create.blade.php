@extends('layouts.dosen')

@section('title', 'Kuesioner Fasilitas - Dosen')
@section('page_title', 'Kuesioner Fasilitas Kampus')

@section('content')
    <div class="container-fluid px-0">

        <!-- Header -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-purple-100 rounded-circle p-3">
                    <i class="bi bi-building fs-3 text-purple-800"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">Kuesioner Fasilitas Kampus</h4>
                    <p class="text-muted mb-0">Silakan berikan penilaian Anda terhadap fasilitas kampus POLMED</p>
                </div>
            </div>
        </div>

        <!-- Form Kuesioner -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <form id="kuesionerForm" method="POST" action="{{ route('dosen.kuesioner-fasilitas.store') }}">
                @csrf

                @php
                    $dimensiList = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
                    $dimensiLabels = [
                        'Tangible' => 'Fasilitas Fisik (Tangible)',
                        'Reliability' => 'Keandalan (Reliability)',
                        'Responsiveness' => 'Daya Tanggap (Responsiveness)',
                        'Assurance' => 'Jaminan (Assurance)',
                        'Empathy' => 'Empati (Empathy)'
                    ];
                @endphp

                @foreach($dimensiList as $dimensi)
                    @if(isset($pertanyaanByDimensi[$dimensi]) && count($pertanyaanByDimensi[$dimensi]) > 0)
                        <div class="card border-0 rounded-4 mb-4 shadow-sm">
                            <div class="card-header bg-purple-100 border-0 rounded-top-4 py-3">
                                <h5 class="fw-semibold mb-0 text-purple-800">
                                    <i class="bi bi-bar-chart-steps me-2"></i>{{ $dimensiLabels[$dimensi] }}
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                @foreach($pertanyaanByDimensi[$dimensi] as $index => $q)
                                    <div class="question-item mb-4 pb-3 border-bottom">
                                        <div class="row align-items-center">
                                            <div class="col-md-5 mb-3 mb-md-0">
                                                <label class="fw-semibold text-gray-800">
                                                    {{ $loop->iteration }}. {{ $q->teks }}
                                                </label>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3 mb-md-0">
                                                        <label class="form-label small text-muted mb-2">
                                                            <i class="bi bi-emoji-smile me-1"></i>Harapan (Seharusnya)
                                                        </label>
                                                        <div class="rating-input">
                                                            <div class="btn-group w-100" role="group">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <input type="radio" name="jawaban[{{ $q->id }}][harapan]"
                                                                        value="{{ $i }}" id="harapan_{{ $q->id }}_{{ $i }}"
                                                                        class="btn-check rating-option" data-question="{{ $q->id }}"
                                                                        data-type="harapan" required>
                                                                    <label class="btn btn-outline-purple rounded-0 rating-label"
                                                                        for="harapan_{{ $q->id }}_{{ $i }}">
                                                                        {{ $i }}
                                                                    </label>
                                                                @endfor
                                                            </div>
                                                            <div class="d-flex justify-content-between mt-1 px-2">
                                                                <small class="text-muted">Sangat Buruk</small>
                                                                <small class="text-muted">Buruk</small>
                                                                <small class="text-muted">Cukup</small>
                                                                <small class="text-muted">Baik</small>
                                                                <small class="text-muted">Sangat Baik</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small text-muted mb-2">
                                                            <i class="bi bi-eye me-1"></i>Persepsi (Kenyataan)
                                                        </label>
                                                        <div class="rating-input">
                                                            <div class="btn-group w-100" role="group">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <input type="radio" name="jawaban[{{ $q->id }}][persepsi]"
                                                                        value="{{ $i }}" id="persepsi_{{ $q->id }}_{{ $i }}"
                                                                        class="btn-check rating-option" data-question="{{ $q->id }}"
                                                                        data-type="persepsi" required>
                                                                    <label class="btn btn-outline-purple rounded-0 rating-label"
                                                                        for="persepsi_{{ $q->id }}_{{ $i }}">
                                                                        {{ $i }}
                                                                    </label>
                                                                @endfor
                                                            </div>
                                                            <div class="d-flex justify-content-between mt-1 px-2">
                                                                <small class="text-muted">Sangat Buruk</small>
                                                                <small class="text-muted">Buruk</small>
                                                                <small class="text-muted">Cukup</small>
                                                                <small class="text-muted">Baik</small>
                                                                <small class="text-muted">Sangat Baik</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Hidden input untuk id_pertanyaan -->
                                        <input type="hidden" name="jawaban[{{ $q->id }}][id_pertanyaan]" value="{{ $q->id }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Tombol Submit -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('dosen.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-purple rounded-pill px-5" id="submitBtn">
                        <i class="bi bi-save me-2"></i>Kirim Kuesioner
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .rating-label {
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 50px;
            text-align: center;
        }

        .rating-label:hover {
            background-color: #f3e8ff;
            color: #4c1d95;
        }

        .btn-check:checked+.rating-label {
            background-color: #4c1d95;
            color: white;
            border-color: #4c1d95;
        }

        .btn-group .rating-label:first-child {
            border-radius: 8px 0 0 8px;
        }

        .btn-group .rating-label:last-child {
            border-radius: 0 8px 8px 0;
        }

        .question-item:last-child {
            border-bottom: none !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('kuesionerForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Validasi semua pertanyaan
            let questions = document.querySelectorAll('.question-item');
            let allValid = true;
            let errorMessage = '';

            questions.forEach((question, idx) => {
                const harapanSelected = question.querySelector('input[name*="[harapan]"]:checked');
                const persepsiSelected = question.querySelector('input[name*="[persepsi]"]:checked');

                if (!harapanSelected) {
                    allValid = false;
                    errorMessage = `Pertanyaan ${idx + 1}: Harapan belum dipilih`;
                    question.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
                if (!persepsiSelected) {
                    allValid = false;
                    errorMessage = `Pertanyaan ${idx + 1}: Persepsi belum dipilih`;
                    question.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
            });

            if (!allValid) {
                Swal.fire('Validasi Gagal', errorMessage, 'warning');
                return;
            }

            // Konfirmasi submit
            Swal.fire({
                title: 'Kirim Kuesioner?',
                text: 'Pastikan Anda telah mengisi semua pertanyaan dengan benar. Jawaban tidak dapat diubah setelah dikirim.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4c1d95',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Mengirim...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // Siapkan data
                    const formData = new FormData(this);

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                    window.location.href = '{{ route("dosen.kuesioner-fasilitas.thankyou") }}';
                                });
                            } else {
                                Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                            }
                        })
                        .catch(error => {
                            if (error.response && error.response.status === 422) {
                                error.response.json().then(err => {
                                    let msg = Object.values(err.errors).flat().join('\n');
                                    Swal.fire('Validasi Gagal', msg, 'error');
                                });
                            } else {
                                Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                            }
                        });
                }
            });
        });

        // Auto-select rating visual feedback
        document.querySelectorAll('.rating-option').forEach(radio => {
            radio.addEventListener('change', function () {
                const questionId = this.dataset.question;
                const type = this.dataset.type;
                const value = this.value;

                // Optional: tambahkan efek visual
                const parent = this.closest('.rating-input');
                const labels = parent.querySelectorAll('.rating-label');
                labels.forEach(label => {
                    if (parseInt(label.textContent) <= parseInt(value)) {
                        label.style.opacity = '1';
                    }
                });
            });
        });
    </script>
@endpush