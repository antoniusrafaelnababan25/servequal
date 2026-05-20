<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'dosen')->with('prodi.jurusan');

        // Filter berdasarkan pencarian teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nidn', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan jurusan (melalui prodi)
        if ($request->filled('jurusan_id')) {
            $query->whereHas('prodi', function ($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
        }

        // Filter berdasarkan prodi
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        // Filter berdasarkan jenjang (sarjana/pascasarjana/internasional)
        if ($request->filled('jenjang')) {
            $query->whereHas('prodi', function ($q) use ($request) {
                $q->where('jenjang', $request->jenjang);
            });
        }

        $dosen = $query->orderBy('name')->paginate(15)->withQueryString();

        // Data untuk dropdown filter
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = Prodi::orderBy('nama_prodi')->get();
        $jenjangList = ['sarjana' => 'Sarjana', 'pascasarjana' => 'Pascasarjana', 'internasional' => 'Internasional'];

        if ($request->ajax()) {
            return response()->json($dosen);
        }

        return view('admin.dosen.index', compact('dosen', 'jurusanList', 'prodiList', 'jenjangList'));
    }

    public function create()
    {
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = collect(); // kosong, akan diisi via AJAX
        return view('admin.dosen.create', compact('jurusanList', 'prodiList'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'nidn' => 'nullable|string|max:20|unique:users,nidn',
            'prodi_id' => 'required|exists:prodi,id',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prodi = Prodi::find($request->prodi_id);

        $dosen = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'dosen',
            'nidn' => $request->nidn,
            'jurusan' => $prodi->jurusan->nama_jurusan, // denormalisasi opsional
            'prodi_id' => $request->prodi_id,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Dosen berhasil ditambahkan', 'data' => $dosen]);
    }

    public function show(User $dosen)
    {
        if ($dosen->role !== 'dosen')
            abort(404);
        $dosen->load('prodi.jurusan');
        return view('admin.dosen.show', compact('dosen'));
    }

    public function edit(User $dosen)
    {
        if ($dosen->role !== 'dosen')
            abort(404);
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = Prodi::where('jurusan_id', $dosen->prodi?->jurusan_id ?? 0)->get();
        return view('admin.dosen.edit', compact('dosen', 'jurusanList', 'prodiList'));
    }

    public function update(Request $request, User $dosen)
    {
        if ($dosen->role !== 'dosen')
            abort(404);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $dosen->id,
            'email' => 'required|email|unique:users,email,' . $dosen->id,
            'nidn' => 'nullable|string|max:20|unique:users,nidn,' . $dosen->id,
            'prodi_id' => 'required|exists:prodi,id',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prodi = Prodi::find($request->prodi_id);

        $dosen->name = $request->name;
        $dosen->username = $request->username;
        $dosen->email = $request->email;
        $dosen->nidn = $request->nidn;
        $dosen->prodi_id = $request->prodi_id;
        $dosen->jurusan = $prodi->jurusan->nama_jurusan; // denormalisasi
        if ($request->filled('password')) {
            $dosen->password = Hash::make($request->password);
        }
        $dosen->save();

        return response()->json(['success' => true, 'message' => 'Dosen berhasil diperbarui']);
    }

    public function destroy(User $dosen)
    {
        if ($dosen->role !== 'dosen')
            abort(404);
        $dosen->delete();
        return response()->json(['success' => true, 'message' => 'Dosen berhasil dihapus']);
    }

    public function toggleActive(User $dosen)
    {
        if ($dosen->role !== 'dosen')
            abort(404);
        $dosen->is_active = !$dosen->is_active;
        $dosen->save();
        return response()->json(['success' => true, 'message' => 'Status dosen berubah', 'is_active' => $dosen->is_active]);
    }

    // API: Ambil prodi berdasarkan jurusan_id
    public function getProdiByJurusan($jurusan_id)
    {
        $prodi = Prodi::where('jurusan_id', $jurusan_id)->orderBy('nama_prodi')->get();
        return response()->json($prodi);
    }
}