<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PertanyaanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pertanyaan::query();

        if ($request->filled('dimensi')) {
            $query->where('dimensi', $request->dimensi);
        }
        if ($request->filled('target_role')) {
            $query->where('target_role', $request->target_role);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        $pertanyaan = $query->orderBy('dimensi')->orderBy('id')->paginate(15)->withQueryString();

        $dimensiList = Pertanyaan::DIMENSIONS;
        $targetRoleList = ['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen'];

        if ($request->ajax()) {
            return response()->json($pertanyaan);
        }

        return view('admin.pertanyaan.index', compact('pertanyaan', 'dimensiList', 'targetRoleList'));
    }

    public function create()
    {
        $dimensiList = Pertanyaan::DIMENSIONS;
        return view('admin.pertanyaan.create', compact('dimensiList'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dimensi' => 'required|in:Tangible,Reliability,Responsiveness,Assurance,Empathy',
            'teks' => 'required|string',
            'target_role' => 'required|in:mahasiswa,dosen',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pertanyaan = Pertanyaan::create([
            'dimensi' => $request->dimensi,
            'teks' => $request->teks,
            'target_role' => $request->target_role,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Pertanyaan berhasil ditambahkan', 'data' => $pertanyaan]);
    }

    public function show(Pertanyaan $pertanyaan)
    {
        return view('admin.pertanyaan.show', compact('pertanyaan'));
    }

    public function edit(Pertanyaan $pertanyaan)
    {
        $dimensiList = Pertanyaan::DIMENSIONS;
        return view('admin.pertanyaan.edit', compact('pertanyaan', 'dimensiList'));
    }

    public function update(Request $request, Pertanyaan $pertanyaan)
    {
        $validator = Validator::make($request->all(), [
            'dimensi' => 'required|in:Tangible,Reliability,Responsiveness,Assurance,Empathy',
            'teks' => 'required|string',
            'target_role' => 'required|in:mahasiswa,dosen',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pertanyaan->update([
            'dimensi' => $request->dimensi,
            'teks' => $request->teks,
            'target_role' => $request->target_role,
        ]);

        return response()->json(['success' => true, 'message' => 'Pertanyaan berhasil diperbarui']);
    }

    public function destroy(Pertanyaan $pertanyaan)
    {
        $pertanyaan->delete();
        return response()->json(['success' => true, 'message' => 'Pertanyaan berhasil dihapus']);
    }

    public function toggleActive(Pertanyaan $pertanyaan)
    {
        $pertanyaan->is_active = !$pertanyaan->is_active;
        $pertanyaan->save();
        return response()->json(['success' => true, 'message' => 'Status pertanyaan berubah', 'is_active' => $pertanyaan->is_active]);
    }
}