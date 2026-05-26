<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\KuesionerPeriode;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KuesionerPeriodeController extends Controller
{
    /**
     * Tampilkan halaman manajemen periode kuesioner
     */
    public function index()
    {
        $periode = KuesionerPeriode::with('prodi.jurusan')->orderBy('created_at', 'desc')->get();
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = Prodi::with('jurusan')->orderBy('nama_prodi')->get();
        $jenjangList = [
            'sarjana' => 'Sarjana',
            'pascasarjana' => 'Pascasarjana',
            'internasional' => 'Internasional',
            'all' => 'Semua Jenjang'
        ];
        $targetRoleList = [
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            'both' => 'Mahasiswa & Dosen'
        ];
        $statusList = [
            'draft' => 'Draft',
            'aktif' => 'Aktif',
            'tutup' => 'Tutup'
        ];

        return view('superadmin.periode.index', compact(
            'periode',
            'jurusanList',
            'prodiList',
            'jenjangList',
            'targetRoleList',
            'statusList'
        ));
    }

    /**
     * Simpan periode baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_periode' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,aktif,tutup',
            'target_role' => 'required|in:mahasiswa,dosen,both',
            'target_jurusan_id' => 'nullable|exists:jurusan,id',
            'target_prodi_id' => 'nullable|exists:prodi,id',
            'target_jenjang' => 'required|in:sarjana,pascasarjana,internasional,all',
            'tujuan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ambil nama jurusan dari ID
        $targetJurusanName = null;
        if ($request->target_jurusan_id) {
            $jurusan = Jurusan::find($request->target_jurusan_id);
            $targetJurusanName = $jurusan ? $jurusan->nama_jurusan : null;
        }

        $periode = KuesionerPeriode::create([
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
            'target_role' => $request->target_role,
            'target_jurusan' => $targetJurusanName,
            'target_prodi_id' => $request->target_prodi_id,
            'target_jenjang' => $request->target_jenjang,
            'tujuan' => $request->tujuan,
            'is_active' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Periode berhasil ditambahkan',
            'data' => $periode
        ]);
    }

    /**
     * Update periode
     */
    public function update(Request $request, $id)
    {
        $periode = KuesionerPeriode::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_periode' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,aktif,tutup',
            'target_role' => 'required|in:mahasiswa,dosen,both',
            'target_jurusan_id' => 'nullable|exists:jurusan,id',
            'target_prodi_id' => 'nullable|exists:prodi,id',
            'target_jenjang' => 'required|in:sarjana,pascasarjana,internasional,all',
            'tujuan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ambil nama jurusan dari ID
        $targetJurusanName = null;
        if ($request->target_jurusan_id) {
            $jurusan = Jurusan::find($request->target_jurusan_id);
            $targetJurusanName = $jurusan ? $jurusan->nama_jurusan : null;
        }

        $periode->update([
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
            'target_role' => $request->target_role,
            'target_jurusan' => $targetJurusanName,
            'target_prodi_id' => $request->target_prodi_id,
            'target_jenjang' => $request->target_jenjang,
            'tujuan' => $request->tujuan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Periode berhasil diperbarui',
            'data' => $periode
        ]);
    }

    /**
     * Hapus periode
     */
    public function destroy($id)
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
    public function toggleActive($id)
    {
        $periode = KuesionerPeriode::findOrFail($id);
        $periode->is_active = !$periode->is_active;
        $periode->save();

        return response()->json([
            'success' => true,
            'message' => $periode->is_active ? 'Periode diaktifkan' : 'Periode dinonaktifkan',
            'is_active' => $periode->is_active
        ]);
    }

    /**
     * Set periode sebagai aktif (dan nonaktifkan semua yang lain)
     */
    public function setActive($id)
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

    /**
     * API: Ambil daftar prodi berdasarkan jurusan_id
     */
    public function getProdiByJurusan($jurusan_id)
    {
        $prodi = Prodi::where('jurusan_id', $jurusan_id)
            ->orderBy('nama_prodi')
            ->get(['id', 'nama_prodi', 'jenjang']);
        return response()->json($prodi);
    }

    /**
     * API: Ambil detail prodi
     */
    public function getProdiDetail($id)
    {
        $prodi = Prodi::with('jurusan')->findOrFail($id);
        return response()->json([
            'id' => $prodi->id,
            'nama_prodi' => $prodi->nama_prodi,
            'jurusan_id' => $prodi->jurusan_id,
            'jenjang' => $prodi->jenjang,
            'jurusan_nama' => $prodi->jurusan->nama_jurusan
        ]);
    }
}