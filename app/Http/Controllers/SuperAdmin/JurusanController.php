<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JurusanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Jurusan::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_jurusan', 'like', "%{$search}%");
        }

        // Ambil data jurusan dengan menghitung jumlah prodi
        $jurusan = $query->withCount('prodi')->orderBy('nama_jurusan')->paginate(15);

        // Untuk response AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $jurusan
            ]);
        }

        return view('superadmin.jurusan.index', compact('jurusan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.jurusan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $request->validate([
            'nama_jurusan' => 'required|string|max:255|unique:jurusan',
            'deskripsi' => 'nullable|string',
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => $request->nama_jurusan,
            'slug' => Str::slug($request->nama_jurusan),
            'deskripsi' => $request->deskripsi,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jurusan berhasil ditambahkan',
                'data' => $jurusan
            ]);
        }

        return redirect()->route('super.jurusan.index')->with('success', 'Jurusan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jurusan = Jurusan::with([
            'prodi' => function ($query) {
                $query->orderBy('nama_prodi');
            }
        ])->findOrFail($id);

        $jumlahProdi = $jurusan->prodi->count();

        return view('superadmin.jurusan.show', compact('jurusan', 'jumlahProdi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jurusan = Jurusan::findOrFail($id);
        return view('superadmin.jurusan.edit', compact('jurusan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jurusan = Jurusan::findOrFail($id);

        $request->validate([
            'nama_jurusan' => 'required|string|max:255|unique:jurusan,nama_jurusan,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $jurusan->update([
            'nama_jurusan' => $request->nama_jurusan,
            'slug' => Str::slug($request->nama_jurusan),
            'deskripsi' => $request->deskripsi,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jurusan berhasil diperbarui',
                'data' => $jurusan
            ]);
        }

        return redirect()->route('super.jurusan.show', $jurusan->id)->with('success', 'Jurusan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jurusan = Jurusan::findOrFail($id);

        // Cek apakah ada prodi terkait
        if ($jurusan->prodi()->count() > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus jurusan yang masih memiliki ' . $jurusan->prodi()->count() . ' program studi'
                ], 422);
            }
            return redirect()->back()->with('error', 'Tidak dapat menghapus jurusan yang masih memiliki program studi');
        }

        $jurusan->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jurusan berhasil dihapus'
            ]);
        }

        return redirect()->route('super.jurusan.index')->with('success', 'Jurusan berhasil dihapus');
    }
}