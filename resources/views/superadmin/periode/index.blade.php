@extends('layouts.superadmin')

@section('title', 'Manajemen Periode Kuesioner - SUPER ADMIN')
@section('page_title', 'Manajemen Periode Kuesioner')

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-end mb-4">
            <button type="button" class="btn btn-purple rounded-pill px-4" data-bs-toggle="modal"
                data-bs-target="#modalPeriode">
                <i class="bi bi-plus-circle me-2"></i>Tambah Periode
            </button>
        </div>

        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Periode</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status</th>
                            <th>Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periode as $p)
                            <tr>
                                <td class="fw-medium">{{ $p->nama_periode }}</td>
                                <td>{{ $p->tanggal_mulai->format('d/m/Y') }}</td>
                                <td>{{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $badge = match ($p->status) {
                                            'aktif' => 'bg-success',
                                            'tutup' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }} rounded-pill px-3 py-1">{{ ucfirst($p->status) }}</span>
                                </td>
                                <td>
                                    @if($p->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1"><i
                                                class="bi bi-check-circle-fill me-1"></i>Aktif</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1"><i
                                                class="bi bi-x-circle-fill me-1"></i>Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-warning rounded-pill me-1 edit-periode"
                                            data-id="{{ $p->id }}" data-nama="{{ $p->nama_periode }}"
                                            data-mulai="{{ $p->tanggal_mulai->format('Y-m-d') }}"
                                            data-selesai="{{ $p->tanggal_selesai->format('Y-m-d') }}"
                                            data-status="{{ $p->status }}" data-jurusan="{{ $p->target_jurusan }}"
                                            data-tujuan="{{ $p->tujuan }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info rounded-pill me-1 toggle-active"
                                            data-id="{{ $p->id }}" data-aktif="{{ $p->is_active ? '1' : '0' }}"
                                            title="Toggle Aktif">
                                            <i class="bi bi-power"></i>
                                        </button>
                                        <button class="btn btn-outline-danger rounded-pill delete-periode"
                                            data-id="{{ $p->id }}" data-nama="{{ $p->nama_periode }}" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Belum ada periode kuesioner</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div class="modal fade" id="modalPeriode" tabindex="-1" aria-labelledby="modalPeriodeLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold" id="modalPeriodeLabel">Tambah Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPeriode">
                    @csrf
                    <input type="hidden" id="methodField" value="POST">
                    <input type="hidden" id="periodeId">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Periode <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nama_periode" id="nama_periode"
                                    class="form-control bg-light border-0 rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Mulai <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                    class="form-control bg-light border-0 rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Selesai <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                    class="form-control bg-light border-0 rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" id="status" class="form-select bg-light border-0 rounded-3">
                                    <option value="draft">Draft</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="tutup">Tutup</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Jurusan (kosong = semua)</label>
                                <input type="text" name="target_jurusan" id="target_jurusan"
                                    class="form-control bg-light border-0 rounded-3"
                                    placeholder="Contoh: teknologi_informasi">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tujuan Kuesioner</label>
                                <textarea name="tujuan" id="tujuan" rows="3"
                                    class="form-control bg-light border-0 rounded-3"
                                    placeholder="Tujuan pelaksanaan kuesioner..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-purple rounded-pill px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Reset modal untuk tambah
        document.querySelector('[data-bs-target="#modalPeriode"]').addEventListener('click', function () {
            document.getElementById('formPeriode').reset();
            document.getElementById('methodField').value = 'POST';
            document.getElementById('modalPeriodeLabel').innerText = 'Tambah Periode';
            document.getElementById('periodeId').value = '';
        });

        // Edit: isi modal dengan data
        document.querySelectorAll('.edit-periode').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('periodeId').value = this.dataset.id;
                document.getElementById('nama_periode').value = this.dataset.nama;
                document.getElementById('tanggal_mulai').value = this.dataset.mulai;
                document.getElementById('tanggal_selesai').value = this.dataset.selesai;
                document.getElementById('status').value = this.dataset.status;
                document.getElementById('target_jurusan').value = this.dataset.jurusan || '';
                document.getElementById('tujuan').value = this.dataset.tujuan || '';
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('modalPeriodeLabel').innerText = 'Edit Periode';
                new bootstrap.Modal(document.getElementById('modalPeriode')).show();
            });
        });

        // Submit form (tambah/edit)
        document.getElementById('formPeriode').addEventListener('submit', function (e) {
            e.preventDefault();
            let id = document.getElementById('periodeId').value;
            let method = document.getElementById('methodField').value;
            let url = method === 'PUT' ? `/super-admin/periode/${id}` : '/super-admin/periode';
            let formData = new FormData(this);
            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }
            axios.post(url, formData)
                .then(res => {
                    if (res.data.success) {
                        Swal.fire('Berhasil', res.data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.data.message, 'error');
                    }
                })
                .catch(err => {
                    if (err.response?.data?.errors) {
                        let msg = Object.values(err.response.data.errors).flat().join('\n');
                        Swal.fire('Validasi Gagal', msg, 'error');
                    } else {
                        Swal.fire('Error', 'Terjadi kesalahan', 'error');
                    }
                });
        });

        // Toggle aktif
        document.querySelectorAll('.toggle-active').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id;
                let isActive = this.dataset.aktif === '1';
                Swal.fire({
                    title: isActive ? 'Nonaktifkan periode?' : 'Aktifkan periode?',
                    text: `Apakah Anda yakin ingin ${isActive ? 'menonaktifkan' : 'mengaktifkan'} periode ini?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4c1d95',
                    confirmButtonText: 'Ya, ubah',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        axios.post(`/super-admin/periode/${id}/toggle-aktif`)
                            .then(res => {
                                if (res.data.success) Swal.fire('Berhasil', res.data.message, 'success').then(() => location.reload());
                                else Swal.fire('Gagal', res.data.message, 'error');
                            })
                            .catch(() => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                    }
                });
            });
        });

        // Hapus periode
        document.querySelectorAll('.delete-periode').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id;
                let nama = this.dataset.nama;
                Swal.fire({
                    title: `Hapus periode "${nama}"?`,
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        axios.delete(`/super-admin/periode/${id}`)
                            .then(res => {
                                if (res.data.success) Swal.fire('Terhapus!', res.data.message, 'success').then(() => location.reload());
                                else Swal.fire('Gagal', res.data.message, 'error');
                            })
                            .catch(() => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                    }
                });
            });
        });
    </script>
@endpush