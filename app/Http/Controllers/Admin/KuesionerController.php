<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\KuesionerPeriode;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KuesionerController extends Controller
{
    /**
     * Tampilkan halaman pengaturan kuesioner + daftar periode
     */
    public function index()
    {
        $status = SystemSetting::get('kuesioner_status', 'closed');
        $targetJurusan = SystemSetting::get('target_jurusan', 'all');
        $tujuan = SystemSetting::get('tujuan_kuesioner', '');

        $periode = KuesionerPeriode::orderBy('created_at', 'desc')->get();
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = Prodi::orderBy('nama_prodi')->get();
        $jenjangList = [
            'sarjana' => 'Sarjana',
            'pascasarjana' => 'Pascasarjana',
            'internasional' => 'Internasional',
            'all' => 'Semua Jenjang'
        ];

        return view('admin.kuesioner.index', compact(
            'status',
            'targetJurusan',
            'tujuan',
            'periode',
            'jurusanList',
            'prodiList',
            'jenjangList'
        ));
    }

    /**
     * Simpan periode baru (CRUD)
     */
    public function storePeriode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_periode' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,aktif,tutup',
            'target_role' => 'required|in:mahasiswa,dosen,both',
            'target_jurusan' => 'nullable|string|max:100',
            'target_prodi_id' => 'nullable|exists:prodi,id',
            'target_jenjang' => 'required|in:sarjana,pascasarjana,internasional,all',
            'tujuan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $periode = KuesionerPeriode::create($request->only([
            'nama_periode',
            'tanggal_mulai',
            'tanggal_selesai',
            'status',
            'target_role',
            'target_jurusan',
            'target_prodi_id',
            'target_jenjang',
            'tujuan'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Periode berhasil ditambahkan',
            'data' => $periode
        ]);
    }

    /**
     * Update periode (targeting, tanggal, status, dll)
     */
    public function updatePeriode(Request $request, $id)
    {
        $periode = KuesionerPeriode::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_periode' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,aktif,tutup',
            'target_role' => 'required|in:mahasiswa,dosen,both',
            'target_jurusan' => 'nullable|string|max:100',
            'target_prodi_id' => 'nullable|exists:prodi,id',
            'target_jenjang' => 'required|in:sarjana,pascasarjana,internasional,all',
            'tujuan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $periode->update($request->only([
            'nama_periode',
            'tanggal_mulai',
            'tanggal_selesai',
            'status',
            'target_role',
            'target_jurusan',
            'target_prodi_id',
            'target_jenjang',
            'tujuan'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Periode berhasil diperbarui',
            'data' => $periode
        ]);
    }

    /**
     * Hapus periode
     */
    public function destroyPeriode($id)
    {
        $periode = KuesionerPeriode::findOrFail($id);
        $periode->delete();
        return response()->json([
            'success' => true,
            'message' => 'Periode berhasil dihapus'
        ]);
    }

    /**
     * Toggle aktif/nonaktif periode
     */
    public function togglePeriodeActive($id)
    {
        $periode = KuesionerPeriode::findOrFail($id);
        $periode->is_active = !$periode->is_active;
        $periode->save();

        return response()->json([
            'success' => true,
            'message' => 'Status periode berhasil diubah',
            'is_active' => $periode->is_active
        ]);
    }

    /**
     * Set periode sebagai aktif (dan nonaktifkan semua yang lain)
     */
    public function setActivePeriode($id)
    {
        // Nonaktifkan semua periode
        KuesionerPeriode::query()->update(['is_active' => false]);

        // Aktifkan periode yang dipilih
        $periode = KuesionerPeriode::findOrFail($id);
        $periode->is_active = true;
        $periode->save();

        return response()->json([
            'success' => true,
            'message' => "Periode {$periode->nama_periode} diaktifkan"
        ]);
    }

    // ==================== PENGATURAN GLOBAL ====================

    /**
     * Update pengaturan global kuesioner (status, target jurusan, tujuan)
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kuesioner_status' => 'required|in:open,closed',
            'target_jurusan' => 'nullable|string|max:100',
            'tujuan_kuesioner' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        SystemSetting::set('kuesioner_status', $request->kuesioner_status);
        SystemSetting::set('target_jurusan', $request->target_jurusan ?? '');
        SystemSetting::set('tujuan_kuesioner', $request->tujuan_kuesioner ?? '');

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan global berhasil disimpan'
        ]);
    }

    /**
     * Toggle status global (buka/tutup)
     */
    public function toggleGlobalStatus()
    {
        $current = SystemSetting::get('kuesioner_status', 'closed');
        $new = $current === 'open' ? 'closed' : 'open';
        SystemSetting::set('kuesioner_status', $new);
        return response()->json([
            'success' => true,
            'status' => $new,
            'message' => "Status kuesioner sekarang " . ($new === 'open' ? 'Terbuka' : 'Tertutup')
        ]);
    }

    // ==================== API UNTUK DROPDOWN PRODI (CASCADING) ====================

    /**
     * API: Ambil daftar prodi berdasarkan jurusan_id (untuk dropdown cascade)
     * @param int $jurusan_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProdiByJurusan($jurusan_id)
    {
        $prodi = Prodi::where('jurusan_id', $jurusan_id)
            ->orderBy('nama_prodi')
            ->get(['id', 'nama_prodi']);
        return response()->json($prodi);
    }

    /**
     * API: Ambil detail prodi berdasarkan id (untuk keperluan edit periode)
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProdiDetail($id)
    {
        $prodi = Prodi::with('jurusan')->findOrFail($id);
        return response()->json([
            'id' => $prodi->id,
            'nama_prodi' => $prodi->nama_prodi,
            'jurusan_id' => $prodi->jurusan_id,
            'jenjang' => $prodi->jenjang,
        ]);
    }
}