<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'mahasiswa')->with('prodi.jurusan');

        // Search text
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Filter by jurusan
        if ($request->filled('jurusan_id')) {
            $query->whereHas('prodi', function ($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
        }

        // Filter by prodi
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        // Filter by jenjang (sarjana/pascasarjana/internasional)
        if ($request->filled('jenjang')) {
            $query->whereHas('prodi', function ($q) use ($request) {
                $q->where('jenjang', $request->jenjang);
            });
        }

        // Filter by kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $mahasiswa = $query->orderBy('name')->paginate(15)->withQueryString();

        // Data for dropdown filters
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = Prodi::orderBy('nama_prodi')->get();
        $jenjangList = ['sarjana' => 'Sarjana', 'pascasarjana' => 'Pascasarjana', 'internasional' => 'Internasional'];

        if ($request->ajax()) {
            return response()->json($mahasiswa);
        }

        return view('admin.mahasiswa.index', compact('mahasiswa', 'jurusanList', 'prodiList', 'jenjangList'));
    }

    public function create()
    {
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = collect(); // empty, will be filled via AJAX
        return view('admin.mahasiswa.create', compact('jurusanList', 'prodiList'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'nim' => 'required|string|max:20|unique:users,nim',
            'prodi_id' => 'required|exists:prodi,id',
            'kelas' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prodi = Prodi::find($request->prodi_id);

        $mahasiswa = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'nim' => $request->nim,
            'prodi_id' => $request->prodi_id,
            'jurusan' => $prodi->jurusan->nama_jurusan, // denormalization
            'kelas' => $request->kelas,
            'tanggal_lahir' => $request->tanggal_lahir,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Mahasiswa berhasil ditambahkan', 'data' => $mahasiswa]);
    }

    public function show(User $mahasiswa)
    {
        if ($mahasiswa->role !== 'mahasiswa')
            abort(404);
        $mahasiswa->load('prodi.jurusan');
        return view('admin.mahasiswa.show', compact('mahasiswa'));
    }

    public function edit(User $mahasiswa)
    {
        if ($mahasiswa->role !== 'mahasiswa')
            abort(404);
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = Prodi::where('jurusan_id', $mahasiswa->prodi?->jurusan_id ?? 0)->get();
        return view('admin.mahasiswa.edit', compact('mahasiswa', 'jurusanList', 'prodiList'));
    }

    public function update(Request $request, User $mahasiswa)
    {
        if ($mahasiswa->role !== 'mahasiswa')
            abort(404);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $mahasiswa->id,
            'email' => 'required|email|unique:users,email,' . $mahasiswa->id,
            'nim' => 'required|string|max:20|unique:users,nim,' . $mahasiswa->id,
            'prodi_id' => 'required|exists:prodi,id',
            'kelas' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prodi = Prodi::find($request->prodi_id);

        $mahasiswa->name = $request->name;
        $mahasiswa->username = $request->username;
        $mahasiswa->email = $request->email;
        $mahasiswa->nim = $request->nim;
        $mahasiswa->prodi_id = $request->prodi_id;
        $mahasiswa->jurusan = $prodi->jurusan->nama_jurusan;
        $mahasiswa->kelas = $request->kelas;
        $mahasiswa->tanggal_lahir = $request->tanggal_lahir;
        if ($request->filled('password')) {
            $mahasiswa->password = Hash::make($request->password);
        }
        $mahasiswa->save();

        return response()->json(['success' => true, 'message' => 'Mahasiswa berhasil diperbarui']);
    }

    public function destroy(User $mahasiswa)
    {
        if ($mahasiswa->role !== 'mahasiswa')
            abort(404);
        $mahasiswa->delete();
        return response()->json(['success' => true, 'message' => 'Mahasiswa berhasil dihapus']);
    }

    public function toggleActive(User $mahasiswa)
    {
        if ($mahasiswa->role !== 'mahasiswa')
            abort(404);
        $mahasiswa->is_active = !$mahasiswa->is_active;
        $mahasiswa->save();
        return response()->json(['success' => true, 'message' => 'Status mahasiswa berubah', 'is_active' => $mahasiswa->is_active]);
    }

    // API: Get prodi by jurusan (for cascading dropdown)
    public function getProdiByJurusan($jurusan_id)
    {
        $prodi = Prodi::where('jurusan_id', $jurusan_id)->orderBy('nama_prodi')->get();
        return response()->json($prodi);
    }
}