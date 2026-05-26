<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Prodi::with('jurusan');

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_prodi', 'like', "%{$search}%");
        }

        $prodi = $query->orderBy('nama_prodi')->paginate(15);
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();

        return view('superadmin.prodi.index', compact('prodi', 'jurusanList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        return view('superadmin.prodi.create', compact('jurusanList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'nama_prodi' => 'required|string|max:255|unique:prodi',
            'jenjang' => 'required|in:sarjana,pascasarjana,internasional',
        ]);

        Prodi::create([
            'jurusan_id' => $request->jurusan_id,
            'nama_prodi' => $request->nama_prodi,
            'jenjang' => $request->jenjang,
            'slug' => Str::slug($request->nama_prodi),
            'is_active' => true,
        ]);

        return redirect()->route('super.prodi.index')->with('success', 'Program Studi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $prodi = Prodi::with('jurusan')->findOrFail($id);
        return view('superadmin.prodi.show', compact('prodi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $prodi = Prodi::findOrFail($id);
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        return view('superadmin.prodi.edit', compact('prodi', 'jurusanList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $prodi = Prodi::findOrFail($id);

        $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'nama_prodi' => 'required|string|max:255|unique:prodi,nama_prodi,' . $id,
            'jenjang' => 'required|in:sarjana,pascasarjana,internasional',
        ]);

        $prodi->update([
            'jurusan_id' => $request->jurusan_id,
            'nama_prodi' => $request->nama_prodi,
            'jenjang' => $request->jenjang,
            'slug' => Str::slug($request->nama_prodi),
        ]);

        return redirect()->route('super.prodi.index')->with('success', 'Program Studi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $prodi = Prodi::findOrFail($id);

        // Cek apakah ada user terkait
        if ($prodi->users()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus program studi yang masih memiliki user');
        }

        $prodi->delete();

        return redirect()->route('super.prodi.index')->with('success', 'Program Studi berhasil dihapus');
    }

    /**
     * Get prodi by jurusan for AJAX
     */
    public function getByJurusan($jurusan_id)
    {
        $prodi = Prodi::where('jurusan_id', $jurusan_id)
            ->orderBy('nama_prodi')
            ->get(['id', 'nama_prodi', 'jenjang']);
        return response()->json($prodi);
    }
}