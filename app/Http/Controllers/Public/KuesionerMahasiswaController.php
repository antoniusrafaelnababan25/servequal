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
     * Halaman validasi NIM & tanggal lahir
     */
    public function showValidasiForm()
    {
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

        // Ambil pertanyaan untuk mahasiswa (target_role = mahasiswa ATAU both)
        $pertanyaanDosen = Pertanyaan::where('tipe_penilaian', 'penilaian_dosen')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('target_role', 'mahasiswa')
                    ->orWhere('target_role', 'both');
            })
            ->orderBy('dimensi')
            ->orderBy('id')
            ->get();

        $pertanyaanDosenPerDimensi = $pertanyaanDosen->groupBy('dimensi');

        // Ambil pertanyaan fasilitas untuk mahasiswa (target_role = mahasiswa ATAU both)
        $pertanyaanFasilitas = Pertanyaan::where('tipe_penilaian', 'penilaian_fasilitas')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('target_role', 'mahasiswa')
                    ->orWhere('target_role', 'both');
            })
            ->orderBy('kategori_fasilitas')
            ->orderBy('dimensi')
            ->orderBy('id')
            ->get();

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
     * Simpan penilaian (dosen atau fasilitas) - FLEKSIBEL TIDAK HARUS SEMUA DIISI
     */
    public function simpan(Request $request)
    {
        Log::info('Simpan request received', $request->all());

        if (!Session::has('mahasiswa_id')) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sesi habis, validasi ulang.'], 401);
            }
            return redirect()->route('public.validasi.form')->with('error', 'Sesi habis.');
        }

        $mahasiswaId = Session::get('mahasiswa_id');
        $mahasiswaNama = Session::get('mahasiswa_nama');
        $mahasiswaNim = Session::get('mahasiswa_nim');
        $kelas = Session::get('kelas');
        $periodeId = Session::get('periode_id');

        $type = $request->input('type', 'dosen');

        if ($type === 'dosen') {
            Log::info('Jawaban dosen:', $request->jawaban ?? []);

            // Validasi hanya dosen_id yang required
            $validator = Validator::make($request->all(), [
                'dosen_id' => 'required|exists:users,id',
                'mata_kuliah' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                Log::error('Validasi gagal:', $validator->errors()->toArray());
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

            // Proses jawaban - hanya ambil yang memiliki harapan DAN persepsi (keduanya harus diisi)
            $jawaban = $request->jawaban ?? [];
            $jawabanValid = [];

            foreach ($jawaban as $idPertanyaan => $item) {
                // Cek apakah harapan dan persepsi keduanya terisi
                if (
                    isset($item['harapan']) && isset($item['persepsi']) &&
                    !empty($item['harapan']) && !empty($item['persepsi'])
                ) {
                    $jawabanValid[$idPertanyaan] = [
                        'id_pertanyaan' => $idPertanyaan,
                        'harapan' => (int) $item['harapan'],
                        'persepsi' => (int) $item['persepsi'],
                    ];
                }
            }

            // Jika tidak ada jawaban yang valid, return error
            if (empty($jawabanValid)) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Harap isi minimal satu pertanyaan dengan lengkap (Harapan dan Persepsi).'], 422);
                }
                return redirect()->back()->with('error', 'Harap isi minimal satu pertanyaan dengan lengkap (Harapan dan Persepsi).');
            }

            // Hitung rata-rata persepsi dari jawaban yang valid
            $rataRata = collect($jawabanValid)->avg('persepsi');

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
                    'nilai' => json_encode($jawabanValid),
                    'rata_rata' => round($rataRata, 2),
                ]);

                Log::info('Penilaian dosen berhasil disimpan', ['id' => $penilaian->id, 'jumlah_pertanyaan' => count($jawabanValid)]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Penilaian dosen berhasil disimpan.',
                        'dosen_id' => $request->dosen_id
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
            Log::info('Jawaban fasilitas:', $request->jawaban_fasilitas ?? []);

            // Validasi untuk fasilitas tidak ada field required
            $validator = Validator::make($request->all(), []);

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

            // Proses jawaban fasilitas - hanya ambil yang memiliki harapan DAN persepsi
            $jawabanFasilitas = $request->jawaban_fasilitas ?? [];
            $jawabanFasilitasValid = [];

            foreach ($jawabanFasilitas as $idPertanyaan => $item) {
                // Cek apakah harapan dan persepsi keduanya terisi
                if (
                    isset($item['harapan']) && isset($item['persepsi']) &&
                    !empty($item['harapan']) && !empty($item['persepsi'])
                ) {
                    $jawabanFasilitasValid[$idPertanyaan] = [
                        'id_pertanyaan' => $idPertanyaan,
                        'harapan' => (int) $item['harapan'],
                        'persepsi' => (int) $item['persepsi'],
                    ];
                }
            }

            // Jika tidak ada jawaban yang valid, return error
            if (empty($jawabanFasilitasValid)) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Harap isi minimal satu pertanyaan fasilitas dengan lengkap (Harapan dan Persepsi).'], 422);
                }
                return redirect()->back()->with('error', 'Harap isi minimal satu pertanyaan fasilitas dengan lengkap (Harapan dan Persepsi).');
            }

            $rataRata = collect($jawabanFasilitasValid)->avg('persepsi');

            try {
                $penilaian = PenilaianFasilitas::create([
                    'periode_id' => $periodeId,
                    'mahasiswa_id' => $mahasiswaId,
                    'mahasiswa_nama' => $mahasiswaNama,
                    'mahasiswa_nim' => $mahasiswaNim,
                    'nilai' => json_encode($jawabanFasilitasValid),
                    'rata_rata' => round($rataRata, 2),
                ]);

                Log::info('Penilaian fasilitas berhasil disimpan', ['id' => $penilaian->id, 'jumlah_pertanyaan' => count($jawabanFasilitasValid)]);

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

        return redirect()->back()->with('error', 'Tipe tidak dikenal.');
    }

    /**
     * Halaman selesai / terima kasih
     */
    public function selesai()
    {
        // Hapus session
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
}