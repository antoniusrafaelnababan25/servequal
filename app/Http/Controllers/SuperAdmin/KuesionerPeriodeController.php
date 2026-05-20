<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\KuesionerPeriode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KuesionerPeriodeController extends Controller
{
    public function index()
    {
        $periode = KuesionerPeriode::orderBy('created_at', 'desc')->get();
        return view('superadmin.periode.index', compact('periode'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_periode' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,aktif,tutup',
            'target_jurusan' => 'nullable|string|max:100',
            'tujuan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $periode = KuesionerPeriode::create($request->all());

        return response()->json(['success' => true, 'message' => 'Periode berhasil ditambahkan', 'data' => $periode]);
    }

    public function update(Request $request, KuesionerPeriode $periode)
    {
        $validator = Validator::make($request->all(), [
            'nama_periode' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,aktif,tutup',
            'target_jurusan' => 'nullable|string|max:100',
            'tujuan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $periode->update($request->all());

        return response()->json(['success' => true, 'message' => 'Periode berhasil diperbarui', 'data' => $periode]);
    }

    public function destroy(KuesionerPeriode $periode)
    {
        $periode->delete();
        return response()->json(['success' => true, 'message' => 'Periode berhasil dihapus']);
    }

    public function toggleActive(KuesionerPeriode $periode)
    {
        $periode->is_active = !$periode->is_active;
        $periode->save();
        return response()->json(['success' => true, 'message' => 'Status periode diubah', 'is_active' => $periode->is_active]);
    }
}