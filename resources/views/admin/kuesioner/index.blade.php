@extends('layouts.admin')

@section('title', 'Pengaturan Kuesioner - Admin')
@section('page_title', 'Pengaturan Kuesioner & Periode')

@section('content')
    <div class="container-fluid px-0">
        <div class="row g-4">
            <!-- Kiri: Pengaturan Global -->
            <div class="col-lg-5">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-gear-fill me-2 text-purple-600"></i>Pengaturan Global</h5>
                    <form id="globalSettingsForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Kuesioner</label>
                            <select name="kuesioner_status" class="form-select bg-light border-0 rounded-3">
                                <option value="open" {{ $status == 'open' ? 'selected' : '' }}>Terbuka (Open)</option>
                                <option value="closed" {{ $status == 'closed' ? 'selected' : '' }}>Tertutup (Closed)</option>
                            </select>
                            <div class="form-text">Hanya berpengaruh jika tidak ada periode aktif.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Target Jurusan (Global)</label>
                            <input type="text" name="target_jurusan" class="form-control bg-light border-0 rounded-3"
                                value="{{ $targetJurusan }}" placeholder="Contoh: teknologi_informasi">
                            <div class="form-text">Kosongkan atau isi "all" untuk semua jurusan.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tujuan Kuesioner</label>
                            <textarea name="tujuan_kuesioner" rows="3"
                                class="form-control bg-light border-0 rounded-3">{{ $tujuan }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-purple rounded-pill px-4">Simpan Pengaturan</button>
                    </form>
                </div>
            </div>

            <!-- Kanan: Daftar Periode -->
            <div class="col-lg-7">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0"><i class="bi bi-calendar-week me-2 text-purple-600"></i>Periode
                            Kuesioner</h5>
                        <button class="btn btn-purple btn-sm rounded-pill" data-bs-toggle="modal"
                            data-bs-target="#periodeModal" onclick="resetModalForm()">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Periode
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Status Periode</th>
                                    <th>Target Role</th>
                                    <th>Target (Jurusan/Prodi/Jenjang)</th>
                                    <th>Aktif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periode as $p)
                                    <tr>
                                        <td class="fw-medium">{{ $p->nama_periode }}</td>
                                        <td>{{ $p->tanggal_mulai->format('d/m/Y') }} -
                                            {{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match ($p->status) {
                                                    'draft' => 'bg-secondary',
                                                    'aktif' => 'bg-success',
                                                    'tutup' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} rounded-pill">{{ ucfirst($p->status) }}</span>
                                        </td>
                                        <td>
                                            @if($p->target_role == 'mahasiswa') Mahasiswa
                                            @elseif($p->target_role == 'dosen') Dosen
                                            @else Mahasiswa & Dosen @endif
                                        </td>
                                        <td>
                                            @php
                                                $targetStr = [];
                                                if ($p->target_jurusan)
                                                    $targetStr[] = "Jurusan: {$p->target_jurusan}";
                                                if ($p->target_prodi_id && $p->prodi)
                                                    $targetStr[] = "Prodi: {$p->prodi->nama_prodi}";
                                                if ($p->target_jenjang && $p->target_jenjang != 'all')
                                                    $targetStr[] = "Jenjang: " . ucfirst($p->target_jenjang);
                                                echo $targetStr ? implode(', ', $targetStr) : 'Semua';
                                            @endphp
                                        </td>
                                        <td>
                                            @if($p->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><i
                                                        class="bi bi-check-circle-fill me-1"></i>Aktif</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill"><i
                                                        class="bi bi-x-circle-fill me-1"></i>Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-info rounded-pill me-1 edit-period"
                                                    data-id="{{ $p->id }}" data-nama="{{ $p->nama_periode }}"
                                                    data-mulai="{{ $p->tanggal_mulai->format('Y-m-d') }}"
                                                    data-selesai="{{ $p->tanggal_selesai->format('Y-m-d') }}"
                                                    data-status="{{ $p->status }}" data-target_role="{{ $p->target_role }}"
                                                    data-target_jurusan="{{ $p->target_jurusan }}"
                                                    data-target_prodi_id="{{ $p->target_prodi_id }}"
                                                    data-target_jenjang="{{ $p->target_jenjang }}"
                                                    data-tujuan="{{ $p->tujuan }}" title="Edit"><i
                                                        class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-purple rounded-pill me-1 set-active"
                                                    data-id="{{ $p->id }}" data-nama="{{ $p->nama_periode }}"
                                                    title="Set Aktif"><i class="bi bi-check-lg"></i></button>
                                                <button class="btn btn-outline-warning rounded-pill me-1 toggle-periode"
                                                    data-id="{{ $p->id }}" data-active="{{ $p->is_active ? '1' : '0' }}"
                                                    title="Toggle Aktif"><i class="bi bi-arrow-repeat"></i></button>
                                                <button class="btn btn-outline-danger rounded-pill delete-period"
                                                    data-id="{{ $p->id }}" data-nama="{{ $p->nama_periode }}" title="Hapus"><i
                                                        class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada periode kuesioner</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Periode -->
    <div class="modal fade" id="periodeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold" id="modalTitle">Tambah Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="periodeForm">
                    @csrf
                    <input type="hidden" id="periode_id" name="periode_id" value="">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Periode <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nama_periode" id="nama_periode"
                                    class="form-control bg-light border-0 rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status Periode</label>
                                <select name="status" id="status" class="form-select bg-light border-0 rounded-3">
                                    <option value="draft">Draft</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="tutup">Tutup</option>
                                </select>
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
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Target Role</label>
                                <select name="target_role" id="target_role" class="form-select bg-light border-0 rounded-3">
                                    <option value="both">Mahasiswa & Dosen</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="dosen">Dosen</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Target Jurusan (teks)</label>
                                <input type="text" name="target_jurusan" id="target_jurusan"
                                    class="form-control bg-light border-0 rounded-3"
                                    placeholder="contoh: teknologi_informasi">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Target Jenjang</label>
                                <select name="target_jenjang" id="target_jenjang"
                                    class="form-select bg-light border-0 rounded-3">
                                    @foreach($jenjangList as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Target Program Studi</label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <select id="filter_jurusan" class="form-select bg-light border-0 rounded-3">
                                            <option value="">Pilih Jurusan (filter prodi)</option>
                                            @foreach($jurusanList as $jur)
                                                <option value="{{ $jur->id }}">{{ $jur->nama_jurusan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="target_prodi_id" id="target_prodi_id"
                                            class="form-select bg-light border-0 rounded-3">
                                            <option value="">Pilih Prodi (opsional)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tujuan Kuesioner (periode ini)</label>
                                <textarea name="tujuan" id="tujuan" rows="3"
                                    class="form-control bg-light border-0 rounded-3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-purple rounded-pill px-4">Simpan Periode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ---------- Global Settings ----------
        document.getElementById('globalSettingsForm').addEventListener('submit', function (e) {
            e.preventDefault();
            let data = {};
            new FormData(this).forEach((v, k) => data[k] = v);
            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            axios.post('{{ route("admin.kuesioner.settings") }}', data)
                .then(res => { if (res.data.success) Swal.fire('Berhasil', res.data.message, 'success'); else Swal.fire('Gagal', res.data.message, 'error'); })
                .catch(err => { if (err.response?.data?.errors) { let msg = Object.values(err.response.data.errors).flat().join('\n'); Swal.fire('Validasi Gagal', msg, 'error'); } else Swal.fire('Error', 'Terjadi kesalahan', 'error'); });
        });

        // ---------- Cascading jurusan -> prodi ----------
        const filterJurusan = document.getElementById('filter_jurusan');
        const targetProdi = document.getElementById('target_prodi_id');
        function loadProdi(jurusanId, selectedId = null) {
            if (!jurusanId) {
                targetProdi.innerHTML = '<option value="">Pilih Prodi (opsional)</option>';
                return;
            }
            axios.get('/admin/kuesioner/prodi-by-jurusan/' + jurusanId)
                .then(res => {
                    targetProdi.innerHTML = '<option value="">Pilih Prodi (opsional)</option>';
                    res.data.forEach(prodi => {
                        let selected = (selectedId == prodi.id) ? 'selected' : '';
                        targetProdi.innerHTML += `<option value="${prodi.id}" ${selected}>${prodi.nama_prodi}</option>`;
                    });
                })
                .catch(() => targetProdi.innerHTML = '<option value="">Gagal memuat prodi</option>');
        }
        filterJurusan.addEventListener('change', () => loadProdi(filterJurusan.value));

        // ---------- Modal: Reset form untuk tambah ----------
        function resetModalForm() {
            document.getElementById('periodeForm').reset();
            document.getElementById('periode_id').value = '';
            document.getElementById('modalTitle').innerText = 'Tambah Periode';
            filterJurusan.value = '';
            targetProdi.innerHTML = '<option value="">Pilih Prodi (opsional)</option>';
        }

        // ---------- Edit Periode ----------
        document.querySelectorAll('.edit-period').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id;
                document.getElementById('periode_id').value = id;
                document.getElementById('nama_periode').value = this.dataset.nama;
                document.getElementById('tanggal_mulai').value = this.dataset.mulai;
                document.getElementById('tanggal_selesai').value = this.dataset.selesai;
                document.getElementById('status').value = this.dataset.status;
                document.getElementById('target_role').value = this.dataset.target_role;
                document.getElementById('target_jurusan').value = this.dataset.target_jurusan || '';
                document.getElementById('target_jenjang').value = this.dataset.target_jenjang || 'all';
                document.getElementById('tujuan').value = this.dataset.tujuan || '';

                let prodiId = this.dataset.target_prodi_id;
                if (prodiId) {
                    // Ambil detail prodi untuk mendapatkan jurusan_id
                    axios.get('/admin/kuesioner/prodi/' + prodiId)
                        .then(res => {
                            let jurusanId = res.data.jurusan_id;
                            filterJurusan.value = jurusanId;
                            loadProdi(jurusanId, prodiId);
                        })
                        .catch(() => { });
                } else {
                    filterJurusan.value = '';
                    targetProdi.innerHTML = '<option value="">Pilih Prodi (opsional)</option>';
                }
                document.getElementById('modalTitle').innerText = 'Edit Periode';
                new bootstrap.Modal(document.getElementById('periodeModal')).show();
            });
        });

        // ---------- Submit Periode (store/update) ----------
        document.getElementById('periodeForm').addEventListener('submit', function (e) {
            e.preventDefault();
            let id = document.getElementById('periode_id').value;
            let url = id ? `/admin/kuesioner/periode/${id}` : '{{ route("admin.kuesioner.periode.store") }}';
            let method = id ? 'PUT' : 'POST';
            let formData = new FormData(this);
            if (method === 'PUT') formData.append('_method', 'PUT');
            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            axios.post(url, formData)
                .then(res => {
                    if (res.data.success) Swal.fire('Berhasil', res.data.message, 'success').then(() => location.reload());
                    else Swal.fire('Gagal', res.data.message, 'error');
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

        // ---------- Set Active Periode (satu aktif, lainnya nonaktif) ----------
        document.querySelectorAll('.set-active').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id, nama = this.dataset.nama;
                Swal.fire({
                    title: `Aktifkan periode ${nama}?`,
                    text: "Periode lain akan dinonaktifkan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4c1d95',
                    confirmButtonText: 'Ya, aktifkan',
                    cancelButtonText: 'Batal'
                }).then(res => {
                    if (res.isConfirmed) {
                        axios.post(`/admin/kuesioner/periode/${id}/set-aktif`)
                            .then(res => {
                                if (res.data.success) Swal.fire('Berhasil', res.data.message, 'success').then(() => location.reload());
                                else Swal.fire('Gagal', res.data.message, 'error');
                            })
                            .catch(() => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                    }
                });
            });
        });

        // ---------- Toggle Aktif (hanya ubah status aktif periode tertentu) ----------
        document.querySelectorAll('.toggle-periode').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id, isActive = this.dataset.active === '1';
                Swal.fire({
                    title: `Ubah status periode?`,
                    text: (isActive ? 'Nonaktifkan' : 'Aktifkan') + ' periode ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4c1d95',
                    confirmButtonText: 'Ya, ubah',
                    cancelButtonText: 'Batal'
                }).then(res => {
                    if (res.isConfirmed) {
                        axios.post(`/admin/kuesioner/periode/${id}/toggle-aktif`)
                            .then(res => {
                                if (res.data.success) Swal.fire('Berhasil', res.data.message, 'success').then(() => location.reload());
                                else Swal.fire('Gagal', res.data.message, 'error');
                            })
                            .catch(() => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                    }
                });
            });
        });

        // ---------- Delete Periode ----------
        document.querySelectorAll('.delete-period').forEach(btn => {
            btn.addEventListener('click', function () {
                let id = this.dataset.id, nama = this.dataset.nama;
                Swal.fire({
                    title: `Hapus periode "${nama}"?`,
                    text: "Data tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then(res => {
                    if (res.isConfirmed) {
                        axios.delete(`/admin/kuesioner/periode/${id}`)
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