<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pertanyaan;
use App\Models\KuesionerPeriode;
use App\Models\PenilaianDosen;
use App\Models\PenilaianFasilitas;
use App\Models\SystemSetting;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KuesionerMahasiswaController extends Controller
{
    /**
     * Batas akhir akses sistem
     * 26 Mei 2026
     */
    const EXPIRY_DATE = '2026-05-26';

    /**
     * Cek apakah sistem masih bisa diakses
     * 
     * @return bool
     */
    private function canAccess(): bool
    {
        $expiryDate = Carbon::parse(self::EXPIRY_DATE)->endOfDay();
        $now = Carbon::now();

        return $now->lessThanOrEqualTo($expiryDate);
    }

    /**
     * Halaman akses ditolak (karena belum bayar)
     * 
     * @return \Illuminate\View\View
     */
    public function accessDenied()
    {
        $expiryDate = Carbon::parse(self::EXPIRY_DATE)->format('d F Y');
        return view('public.kuesioner.access_denied', compact('expiryDate'));
    }

    /**
     * Halaman validasi NIM & tanggal lahir
     */
    public function showValidasiForm()
    {
        // CEK TANGGAL - Jika melewati 26 Mei 2026, TAMPILKAN HALAMAN AKSES DITOLAK
        if (!$this->canAccess()) {
            return redirect()->route('public.access.denied');
        }

        // Cek apakah ada periode aktif atau status global open
        $periodeAktif = KuesionerPeriode::where('is_active', true)
            ->where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->first();

        $statusGlobal = SystemSetting::get('kuesioner_status', 'closed');

        if (!$periodeAktif && $statusGlobal !== 'open') {
            return view('public.kuesioner.closed', ['message' => 'Kuesioner sedang ditutup.']);
        }

        // Kirim data statistik ke view
        $totalDosen = User::where('role', 'dosen')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalPenilaian = PenilaianDosen::count();
        $rataKepuasan = PenilaianDosen::avg('rata_rata') ?? 0;

        return view('public.kuesioner.validasi', compact('totalDosen', 'totalMahasiswa', 'totalPenilaian', 'rataKepuasan'));
    }

    /**
     * Proses validasi NIM & tanggal lahir
     */
    public function validasi(Request $request)
    {
        // CEK TANGGAL - Jika melewati 26 Mei 2026, TOLAK AKSES
        if (!$this->canAccess()) {
            return redirect()->route('public.access.denied');
        }

        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $mahasiswa = User::where('role', 'mahasiswa')
            ->where('nim', $request->nim)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        if (!$mahasiswa) {
            return redirect()->back()->with('error', 'NIM atau tanggal lahir tidak valid.')->withInput();
        }

        if (!$mahasiswa->is_active) {
            return redirect()->back()->with('error', 'Akun Anda tidak aktif. Hubungi admin.');
        }

        // Ambil periode aktif
        $periodeAktif = KuesionerPeriode::where('is_active', true)
            ->where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->first();

        if (!$periodeAktif) {
            return redirect()->back()->with('error', 'Tidak ada periode kuesioner aktif.');
        }

        // Validasi target periode
        if ($periodeAktif->target_role !== 'both' && $periodeAktif->target_role !== 'mahasiswa') {
            return redirect()->back()->with('error', 'Periode ini tidak untuk mahasiswa.');
        }

        if ($periodeAktif->target_jurusan && $periodeAktif->target_jurusan !== 'all') {
            if ($mahasiswa->jurusan !== $periodeAktif->target_jurusan) {
                return redirect()->back()->with('error', 'Kuesioner hanya untuk jurusan ' . $periodeAktif->target_jurusan);
            }
        }

        if ($periodeAktif->target_prodi_id && $mahasiswa->prodi_id != $periodeAktif->target_prodi_id) {
            $prodiTarget = Prodi::find($periodeAktif->target_prodi_id);
            return redirect()->back()->with('error', 'Kuesioner hanya untuk program studi ' . ($prodiTarget->nama_prodi ?? 'tertentu'));
        }

        if ($periodeAktif->target_jenjang && $periodeAktif->target_jenjang !== 'all') {
            $prodi = $mahasiswa->prodi;
            if (!$prodi || $prodi->jenjang !== $periodeAktif->target_jenjang) {
                return redirect()->back()->with('error', 'Kuesioner hanya untuk jenjang ' . $periodeAktif->target_jenjang);
            }
        }

        // Simpan ke session
        Session::put([
            'mahasiswa_id' => $mahasiswa->id,
            'mahasiswa_nama' => $mahasiswa->name,
            'mahasiswa_nim' => $mahasiswa->nim,
            'kelas' => $mahasiswa->kelas,
            'jurusan' => $mahasiswa->jurusan,
            'prodi_id' => $mahasiswa->prodi_id,
            'periode_id' => $periodeAktif->id,
            'periode_nama' => $periodeAktif->nama_periode,
            'periode_tanggal_mulai' => $periodeAktif->tanggal_mulai->format('d/m/Y'),
            'periode_tanggal_selesai' => $periodeAktif->tanggal_selesai->format('d/m/Y'),
            'periode_target' => $periodeAktif->target_jurusan ?? 'all',
        ]);

        return redirect()->route('public.isi.form');
    }

    /**
     * Tampilkan form kuesioner
     */
    public function isiForm()
    {
        // CEK TANGGAL - Jika melewati 26 Mei 2026, TOLAK AKSES
        if (!$this->canAccess()) {
            return redirect()->route('public.access.denied');
        }

        if (!Session::has('mahasiswa_id')) {
            return redirect()->route('public.validasi.form')->with('error', 'Silakan validasi ulang.');
        }

        $mahasiswaId = Session::get('mahasiswa_id');
        $jurusan = Session::get('jurusan');
        $periodeId = Session::get('periode_id');
        $periodeNama = Session::get('periode_nama');
        $periodeMulai = Session::get('periode_tanggal_mulai');
        $periodeSelesai = Session::get('periode_tanggal_selesai');

        // Jika session periode kosong, ambil dari database
        if (!$periodeNama && $periodeId) {
            $periode = KuesionerPeriode::find($periodeId);
            if ($periode) {
                $periodeNama = $periode->nama_periode;
                $periodeMulai = $periode->tanggal_mulai->format('d/m/Y');
                $periodeSelesai = $periode->tanggal_selesai->format('d/m/Y');
                Session::put([
                    'periode_nama' => $periodeNama,
                    'periode_tanggal_mulai' => $periodeMulai,
                    'periode_tanggal_selesai' => $periodeSelesai,
                ]);
            }
        }

        // Ambil daftar dosen dari jurusan yang sama
        $dosenList = User::where('role', 'dosen')
            ->where('jurusan', $jurusan)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'nidn']);

        foreach ($dosenList as $dosen) {
            $dosen->already_rated = PenilaianDosen::where('mahasiswa_id', $mahasiswaId)
                ->where('dosen_id', $dosen->id)
                ->where('periode_id', $periodeId)
                ->exists();
        }

        // Ambil pertanyaan untuk mahasiswa (penilaian dosen)
        $pertanyaanDosen = Pertanyaan::where('target_role', 'mahasiswa')
            ->where('tipe_penilaian', 'penilaian_dosen')
            ->where('is_active', true)
            ->orderBy('dimensi')
            ->orderBy('id')
            ->get();

        // Ambil pertanyaan untuk fasilitas (penilaian fasilitas)
        $pertanyaanFasilitas = Pertanyaan::where('tipe_penilaian', 'penilaian_fasilitas')
            ->where('is_active', true)
            ->orderBy('kategori_fasilitas')
            ->orderBy('dimensi')
            ->orderBy('id')
            ->get();

        $pertanyaanDosenPerDimensi = $pertanyaanDosen->groupBy('dimensi');
        $pertanyaanFasilitasPerKategori = $pertanyaanFasilitas->groupBy('kategori_fasilitas');

        $fasilitasSudahDiisi = PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)
            ->where('periode_id', $periodeId)
            ->exists();

        // Hitung jumlah dosen yang sudah dinilai
        $jumlahDosenDinilai = PenilaianDosen::where('mahasiswa_id', $mahasiswaId)
            ->where('periode_id', $periodeId)
            ->count();

        $totalDosen = $dosenList->count();
        $progressDosen = $totalDosen > 0 ? round(($jumlahDosenDinilai / $totalDosen) * 100) : 0;

        return view('public.kuesioner.isi', compact(
            'dosenList',
            'pertanyaanDosenPerDimensi',
            'pertanyaanFasilitasPerKategori',
            'fasilitasSudahDiisi',
            'periodeNama',
            'periodeMulai',
            'periodeSelesai',
            'jumlahDosenDinilai',
            'totalDosen',
            'progressDosen'
        ));
    }

    /**
     * Simpan penilaian (dosen atau fasilitas)
     */
    public function simpan(Request $request)
    {
        // CEK TANGGAL - Jika melewati 26 Mei 2026, TOLAK AKSES
        if (!$this->canAccess()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Masa berlaku sistem telah berakhir.'
                ], 403);
            }
            return redirect()->route('public.access.denied');
        }

        // Debug log
        Log::info('Simpan request received', $request->all());

        if (!Session::has('mahasiswa_id')) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sesi habis, silakan validasi ulang.'], 401);
            }
            return redirect()->route('public.validasi.form')->with('error', 'Sesi habis, silakan validasi ulang.');
        }

        $mahasiswaId = Session::get('mahasiswa_id');
        $mahasiswaNama = Session::get('mahasiswa_nama');
        $mahasiswaNim = Session::get('mahasiswa_nim');
        $kelas = Session::get('kelas');
        $periodeId = Session::get('periode_id');

        $type = $request->input('type', 'dosen');

        if ($type === 'dosen') {
            // Debug struktur jawaban
            Log::info('Jawaban dosen:', $request->jawaban ?? []);

            $validator = Validator::make($request->all(), [
                'dosen_id' => 'required|exists:users,id',
                'jawaban' => 'required|array|min:1',
                'mata_kuliah' => 'nullable|string|max:100',
            ]);

            // Validasi setiap jawaban
            if ($validator->passes() && is_array($request->jawaban)) {
                foreach ($request->jawaban as $key => $jawaban) {
                    $validator->addRules([
                        "jawaban.{$key}.id_pertanyaan" => 'required|exists:pertanyaan,id',
                        "jawaban.{$key}.harapan" => 'required|integer|min:1|max:5',
                        "jawaban.{$key}.persepsi" => 'required|integer|min:1|max:5',
                    ]);
                }
            }

            if ($validator->fails()) {
                Log::error('Validasi dosen gagal:', $validator->errors()->toArray());
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $dosen = User::find($request->dosen_id);
            if (!$dosen || $dosen->role !== 'dosen') {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Dosen tidak valid.'], 400);
                }
                return redirect()->back()->with('error', 'Dosen tidak valid.');
            }

            // Cek duplikat
            $exists = PenilaianDosen::where('mahasiswa_id', $mahasiswaId)
                ->where('dosen_id', $request->dosen_id)
                ->where('periode_id', $periodeId)
                ->exists();

            if ($exists) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Anda sudah menilai dosen ini.'], 409);
                }
                return redirect()->back()->with('error', 'Anda sudah menilai dosen ini.');
            }

            // Hitung rata-rata persepsi
            $rataRata = collect($request->jawaban)->avg('persepsi');

            try {
                $penilaian = PenilaianDosen::create([
                    'periode_id' => $periodeId,
                    'dosen_id' => $request->dosen_id,
                    'dosen_nama' => $dosen->name,
                    'mahasiswa_id' => $mahasiswaId,
                    'mahasiswa_nama' => $mahasiswaNama,
                    'mahasiswa_nim' => $mahasiswaNim,
                    'kelas' => $kelas,
                    'mata_kuliah' => $request->mata_kuliah,
                    'nilai' => json_encode($request->jawaban),
                    'rata_rata' => round($rataRata, 2),
                ]);

                Log::info('Penilaian dosen berhasil disimpan', ['id' => $penilaian->id]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Penilaian dosen berhasil disimpan.',
                        'dosen_id' => $request->dosen_id,
                        'dosen_nama' => $dosen->name
                    ]);
                }
                return redirect()->route('public.isi.form')->with('success', 'Penilaian dosen berhasil disimpan.');
            } catch (\Exception $e) {
                Log::error('Error saving dosen penilaian: ' . $e->getMessage());
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
                }
                return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
            }
        } elseif ($type === 'fasilitas') {
            // Debug struktur jawaban fasilitas
            Log::info('Jawaban fasilitas:', $request->jawaban_fasilitas ?? []);

            $validator = Validator::make($request->all(), [
                'jawaban_fasilitas' => 'required|array|min:1',
            ]);

            // Validasi setiap jawaban fasilitas
            if ($validator->passes() && is_array($request->jawaban_fasilitas)) {
                foreach ($request->jawaban_fasilitas as $key => $jawaban) {
                    $validator->addRules([
                        "jawaban_fasilitas.{$key}.id_pertanyaan" => 'required|exists:pertanyaan,id',
                        "jawaban_fasilitas.{$key}.harapan" => 'required|integer|min:1|max:5',
                        "jawaban_fasilitas.{$key}.persepsi" => 'required|integer|min:1|max:5',
                    ]);
                }
            }

            if ($validator->fails()) {
                Log::error('Validasi fasilitas gagal:', $validator->errors()->toArray());
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Cek duplikat fasilitas
            $exists = PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)
                ->where('periode_id', $periodeId)
                ->exists();

            if ($exists) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Anda sudah mengisi penilaian fasilitas.'], 409);
                }
                return redirect()->back()->with('error', 'Anda sudah mengisi penilaian fasilitas.');
            }

            $rataRata = collect($request->jawaban_fasilitas)->avg('persepsi');

            try {
                $penilaian = PenilaianFasilitas::create([
                    'periode_id' => $periodeId,
                    'mahasiswa_id' => $mahasiswaId,
                    'mahasiswa_nama' => $mahasiswaNama,
                    'mahasiswa_nim' => $mahasiswaNim,
                    'nilai' => json_encode($request->jawaban_fasilitas),
                    'rata_rata' => round($rataRata, 2),
                ]);

                Log::info('Penilaian fasilitas berhasil disimpan', ['id' => $penilaian->id]);

                if ($request->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Penilaian fasilitas berhasil disimpan.']);
                }
                return redirect()->route('public.isi.form')->with('success', 'Penilaian fasilitas berhasil disimpan.');
            } catch (\Exception $e) {
                Log::error('Error saving fasilitas penilaian: ' . $e->getMessage());
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
                }
                return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Tipe penilaian tidak dikenal.');
    }

    /**
     * Halaman selesai / terima kasih
     */
    public function selesai()
    {
        // Bersihkan session
        Session::forget([
            'mahasiswa_id',
            'mahasiswa_nama',
            'mahasiswa_nim',
            'kelas',
            'jurusan',
            'prodi_id',
            'periode_id',
            'periode_nama',
            'periode_tanggal_mulai',
            'periode_tanggal_selesai',
            'periode_target'
        ]);

        return view('public.kuesioner.selesai');
    }

    /**
     * Ambil data dosen yang sudah dinilai (untuk update UI)
     */
    public function getRatedDosen()
    {
        if (!Session::has('mahasiswa_id')) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid'], 401);
        }

        $mahasiswaId = Session::get('mahasiswa_id');
        $periodeId = Session::get('periode_id');

        $ratedDosenIds = PenilaianDosen::where('mahasiswa_id', $mahasiswaId)
            ->where('periode_id', $periodeId)
            ->pluck('dosen_id')
            ->toArray();

        return response()->json(['success' => true, 'rated_dosen_ids' => $ratedDosenIds]);
    }
}