@extends('layouts.superadmin')

@section('title', 'Laporan Penilaian Fasilitas - Super Admin')
@section('page_title', 'Laporan Penilaian Fasilitas')

@section('content')
    <div class="container-fluid px-0">
        <!-- Filter Periode -->
        <div class="bg-white rounded-4 shadow-sm p-3 mb-4">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-0">
                        <i class="bi bi-calendar-week me-2 text-purple-600"></i>Periode Kuesioner
                    </label>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('super.laporan.fasilitas') }}" id="periodeForm">
                        <select name="periode_id" class="form-select bg-light border-0 rounded-3"
                            onchange="this.form.submit()">
                            <option value="">-- Semua Periode --</option>
                            @foreach($periodeList as $periode)
                                <option value="{{ $periode->id }}" {{ $periodeTerpilih && $periodeTerpilih->id == $periode->id ? 'selected' : '' }}>
                                    {{ $periode->nama_periode }}
                                    ({{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    @if($periodeTerpilih)
                        <span class="badge bg-purple-100 text-purple-800 px-3 py-2">Menampilkan:
                            {{ $periodeTerpilih->nama_periode }}</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">Menampilkan semua periode</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <form method="GET" action="{{ route('super.laporan.fasilitas') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Mahasiswa</label>
                        <select name="mahasiswa_id" class="form-select bg-light border-0 rounded-3">
                            <option value="">Semua Mahasiswa</option>
                            @foreach($mahasiswaList as $m)
                                <option value="{{ $m->id }}" {{ request('mahasiswa_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Jurusan</label>
                        <select name="jurusan_id" class="form-select bg-light border-0 rounded-3">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusanList as $j)
                                <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">&nbsp;</label>
                        <button type="submit" class="btn btn-purple rounded-pill w-100"><i
                                class="bi bi-funnel me-1"></i>Filter</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control bg-light border-0 rounded-3"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control bg-light border-0 rounded-3"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4 text-end">
                        @if(request()->anyFilled(['mahasiswa_id', 'jurusan_id', 'start_date', 'end_date']))
                            <a href="{{ route('super.laporan.fasilitas') }}"
                                class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel -->
        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0"><i class="bi bi-table me-2 text-purple-600"></i>Data Penilaian Fasilitas</h5>
                <span class="text-muted small">Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari
                    {{ $data->total() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>NIM</th>
                            <th>Jurusan</th>
                            <th>Rata-rata</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                            <tr>
                                <td class="text-center">{{ $data->firstItem() + $index }}</td>
                                <td class="fw-medium">{{ $item->mahasiswa_nama }}</td>
                                <td>{{ $item->mahasiswa_nim }}</td>
                                <td>{{ $item->mahasiswa->jurusan ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-purple-100 text-purple-800 rounded-pill px-3 py-1">
                                        {{ number_format($item->rata_rata, 2) }}
                                    </span>
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">Belum ada data penilaian fasilitas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $data->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection