<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PertanyaanController extends Controller
{
    /**
     * Display a listing of the questions.
     */
    public function index(Request $request)
    {
        try {
            $query = Pertanyaan::query();

            if ($request->filled('tipe_penilaian')) {
                $query->where('tipe_penilaian', $request->tipe_penilaian);
            }

            if ($request->filled('dimensi')) {
                $query->where('dimensi', $request->dimensi);
            }

            if ($request->filled('target_role')) {
                $query->where('target_role', $request->target_role);
            }

            if ($request->filled('status')) {
                $query->where('is_active', $request->status == 'active');
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('teks', 'like', "%{$search}%");
            }

            $pertanyaan = $query->orderBy('created_at', 'desc')->paginate(10);

            $dimensiList = Pertanyaan::DIMENSIONS;
            $tipePenilaianList = Pertanyaan::TYPES;
            $targetRoleList = Pertanyaan::TARGET_ROLES;
            $kategoriFasilitasList = Pertanyaan::KATEGORI_FASILITAS;

            return view('admin.pertanyaan.index', compact(
                'pertanyaan',
                'dimensiList',
                'tipePenilaianList',
                'targetRoleList',
                'kategoriFasilitasList'
            ));
        } catch (\Exception $e) {
            Log::error('Error in PertanyaanController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        try {
            $dimensiList = Pertanyaan::DIMENSIONS;
            $tipePenilaianList = Pertanyaan::TYPES;
            $targetRoleList = Pertanyaan::TARGET_ROLES;
            $kategoriFasilitasList = Pertanyaan::KATEGORI_FASILITAS;

            return view('admin.pertanyaan.create', compact(
                'dimensiList',
                'tipePenilaianList',
                'targetRoleList',
                'kategoriFasilitasList'
            ));
        } catch (\Exception $e) {
            Log::error('Error in PertanyaanController@create: ' . $e->getMessage());
            return redirect()->route('admin.pertanyaan.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'tipe_penilaian' => 'required|in:penilaian_dosen,penilaian_fasilitas',
                'dimensi' => 'required|string|in:Tangible,Reliability,Responsiveness,Assurance,Empathy',
                'target_role' => 'required|in:mahasiswa,dosen,both',
                'teks' => 'required|string|min:5|max:500',
            ];

            if ($request->tipe_penilaian == 'penilaian_fasilitas') {
                $rules['kategori_fasilitas'] = 'required|string|in:umum,peralatan,ruangan,akses,infrastruktur';
            }

            $messages = [
                'tipe_penilaian.required' => 'Tipe penilaian harus dipilih',
                'tipe_penilaian.in' => 'Tipe penilaian tidak valid',
                'dimensi.required' => 'Dimensi harus dipilih',
                'dimensi.in' => 'Dimensi tidak valid',
                'target_role.required' => 'Target responden harus dipilih',
                'target_role.in' => 'Target responden tidak valid',
                'teks.required' => 'Teks pertanyaan harus diisi',
                'teks.min' => 'Teks pertanyaan minimal 5 karakter',
                'teks.max' => 'Teks pertanyaan maksimal 500 karakter',
                'kategori_fasilitas.required' => 'Kategori fasilitas harus dipilih',
                'kategori_fasilitas.in' => 'Kategori fasilitas tidak valid',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'tipe_penilaian' => $request->tipe_penilaian,
                'dimensi' => trim($request->dimensi),
                'target_role' => $request->target_role,
                'teks' => trim($request->teks),
                'is_active' => true,
            ];

            if ($request->tipe_penilaian == 'penilaian_fasilitas') {
                $data['kategori_fasilitas'] = $request->kategori_fasilitas;
            }

            $pertanyaan = Pertanyaan::create($data);

            Cache::forget('pertanyaan_list');

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil ditambahkan',
                'data' => $pertanyaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PertanyaanController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified question.
     */
    public function show($id)
    {
        try {
            $pertanyaan = Pertanyaan::findOrFail($id);
            $kategoriFasilitasList = Pertanyaan::KATEGORI_FASILITAS;

            return view('admin.pertanyaan.show', compact('pertanyaan', 'kategoriFasilitasList'));
        } catch (\Exception $e) {
            Log::error('Error in PertanyaanController@show: ' . $e->getMessage());
            return redirect()->route('admin.pertanyaan.index')->with('error', 'Pertanyaan tidak ditemukan');
        }
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit($id)
    {
        try {
            $pertanyaan = Pertanyaan::findOrFail($id);
            $dimensiList = Pertanyaan::DIMENSIONS;
            $tipePenilaianList = Pertanyaan::TYPES;
            $targetRoleList = Pertanyaan::TARGET_ROLES;
            $kategoriFasilitasList = Pertanyaan::KATEGORI_FASILITAS;

            return view('admin.pertanyaan.edit', compact(
                'pertanyaan',
                'dimensiList',
                'tipePenilaianList',
                'targetRoleList',
                'kategoriFasilitasList'
            ));
        } catch (\Exception $e) {
            Log::error('Error in PertanyaanController@edit: ' . $e->getMessage());
            return redirect()->route('admin.pertanyaan.index')->with('error', 'Pertanyaan tidak ditemukan');
        }
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $pertanyaan = Pertanyaan::findOrFail($id);

            $rules = [
                'tipe_penilaian' => 'required|in:penilaian_dosen,penilaian_fasilitas',
                'dimensi' => 'required|string|in:Tangible,Reliability,Responsiveness,Assurance,Empathy',
                'target_role' => 'required|in:mahasiswa,dosen,both',
                'teks' => 'required|string|min:5|max:500',
            ];

            if ($request->tipe_penilaian == 'penilaian_fasilitas') {
                $rules['kategori_fasilitas'] = 'required|string|in:umum,peralatan,ruangan,akses,infrastruktur';
            }

            $messages = [
                'tipe_penilaian.required' => 'Tipe penilaian harus dipilih',
                'tipe_penilaian.in' => 'Tipe penilaian tidak valid',
                'dimensi.required' => 'Dimensi harus dipilih',
                'dimensi.in' => 'Dimensi tidak valid',
                'target_role.required' => 'Target responden harus dipilih',
                'target_role.in' => 'Target responden tidak valid',
                'teks.required' => 'Teks pertanyaan harus diisi',
                'teks.min' => 'Teks pertanyaan minimal 5 karakter',
                'teks.max' => 'Teks pertanyaan maksimal 500 karakter',
                'kategori_fasilitas.required' => 'Kategori fasilitas harus dipilih',
                'kategori_fasilitas.in' => 'Kategori fasilitas tidak valid',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'tipe_penilaian' => $request->tipe_penilaian,
                'dimensi' => trim($request->dimensi),
                'target_role' => $request->target_role,
                'teks' => trim($request->teks),
            ];

            if ($request->tipe_penilaian == 'penilaian_fasilitas') {
                $data['kategori_fasilitas'] = $request->kategori_fasilitas;
            } else {
                $data['kategori_fasilitas'] = null;
            }

            if ($request->has('is_active')) {
                $data['is_active'] = $request->is_active == '1' || $request->is_active === 'on';
            }

            $pertanyaan->update($data);

            Cache::forget('pertanyaan_list');

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PertanyaanController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy($id)
    {
        try {
            $pertanyaan = Pertanyaan::findOrFail($id);
            $pertanyaan->delete();

            Cache::forget('pertanyaan_list');

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PertanyaanController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pertanyaan'
            ], 500);
        }
    }

    /**
     * Toggle active status of the specified question.
     */
    public function toggleActive($id)
    {
        try {
            $pertanyaan = Pertanyaan::findOrFail($id);
            $pertanyaan->is_active = !$pertanyaan->is_active;
            $pertanyaan->save();

            Cache::forget('pertanyaan_list');

            return response()->json([
                'success' => true,
                'message' => $pertanyaan->is_active ? 'Pertanyaan diaktifkan' : 'Pertanyaan dinonaktifkan',
                'is_active' => $pertanyaan->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PertanyaanController@toggleActive: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status pertanyaan'
            ], 500);
        }
    }
}