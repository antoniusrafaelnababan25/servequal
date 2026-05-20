<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user (dengan filter & pagination)
     */
    public function index(Request $request)
    {
        $query = User::with('prodi.jurusan');

        if ($request->filled('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nidn', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json($users);
        }

        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $prodis = Prodi::with('jurusan')->orderBy('nama_prodi')->get();

        return view('superadmin.users.index', compact('users', 'jurusans', 'prodis'));
    }

    /**
     * Menampilkan form tambah user
     */
    public function create()
    {
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $prodis = Prodi::with('jurusan')->orderBy('nama_prodi')->get();

        return view('superadmin.users.create', compact('jurusans', 'prodis'));
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:super_admin,admin,dosen,mahasiswa',
            'tanggal_lahir' => 'nullable|date',
            'is_active' => 'boolean',
        ];

        // Validasi berdasarkan role
        if ($request->role === 'dosen') {
            $rules['nidn'] = 'required|string|max:20|unique:users,nidn';
            $rules['jurusan'] = 'nullable|exists:jurusan,id';
            $rules['prodi_id'] = 'nullable|exists:prodi,id';
            $rules['username'] = 'nullable|string|max:50|unique:users,username';
            $rules['password'] = 'nullable|min:6|confirmed';
        } elseif ($request->role === 'mahasiswa') {
            $rules['nim'] = 'required|string|max:20|unique:users,nim';
            $rules['kelas'] = 'nullable|string|max:20';
            $rules['jurusan'] = 'nullable|exists:jurusan,id';
            $rules['prodi_id'] = 'nullable|exists:prodi,id';
            $rules['username'] = 'nullable|string|max:50|unique:users,username';
            $rules['password'] = 'nullable|min:6|confirmed';
        } elseif ($request->role === 'super_admin' || $request->role === 'admin') {
            $rules['username'] = 'required|string|max:50|unique:users,username';
            $rules['password'] = 'required|min:6|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'tanggal_lahir' => $request->tanggal_lahir,
            'is_active' => $request->boolean('is_active', true),
        ];

        // Set username dan password berdasarkan role
        if ($request->role === 'super_admin' || $request->role === 'admin') {
            $userData['username'] = $request->username;
            $userData['password'] = Hash::make($request->password);
        } elseif ($request->role === 'dosen') {
            $userData['nidn'] = $request->nidn;
            $userData['username'] = $request->username ?? $request->nidn;
            $userData['password'] = $request->filled('password') ? Hash::make($request->password) : Hash::make($request->nidn);
            $userData['jurusan'] = $request->jurusan;
            $userData['prodi_id'] = $request->prodi_id;
        } elseif ($request->role === 'mahasiswa') {
            $userData['nim'] = $request->nim;
            $userData['kelas'] = $request->kelas;
            $userData['username'] = $request->username ?? $request->nim;
            $userData['password'] = $request->filled('password') ? Hash::make($request->password) : Hash::make($request->nim);
            $userData['jurusan'] = $request->jurusan;
            $userData['prodi_id'] = $request->prodi_id;
        }

        $user = User::create($userData);

        return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan', 'user' => $user]);
    }

    /**
     * Detail user
     */
    public function show(User $user)
    {
        $user->load('prodi.jurusan');

        if (request()->ajax()) {
            return response()->json(['success' => true, 'user' => $user]);
        }

        return view('superadmin.users.show', compact('user'));
    }

    /**
     * Form edit user
     */
    public function edit(User $user)
    {
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();

        // Load prodi yang sudah dipilih user
        $selectedProdi = null;
        if ($user->prodi_id) {
            $selectedProdi = Prodi::with('jurusan')->find($user->prodi_id);
        }

        $prodis = Prodi::with('jurusan')->orderBy('nama_prodi')->get();

        return view('superadmin.users.edit', compact('user', 'jurusans', 'prodis', 'selectedProdi'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,admin,dosen,mahasiswa',
            'tanggal_lahir' => 'nullable|date',
            'is_active' => 'boolean',
        ];

        // Validasi berdasarkan role
        if ($request->role === 'dosen') {
            $rules['nidn'] = 'required|string|max:20|unique:users,nidn,' . $user->id;
            $rules['jurusan'] = 'nullable|exists:jurusan,id';
            $rules['prodi_id'] = 'nullable|exists:prodi,id';
            $rules['username'] = 'nullable|string|max:50|unique:users,username,' . $user->id;
            $rules['password'] = 'nullable|min:6|confirmed';
        } elseif ($request->role === 'mahasiswa') {
            $rules['nim'] = 'required|string|max:20|unique:users,nim,' . $user->id;
            $rules['kelas'] = 'nullable|string|max:20';
            $rules['jurusan'] = 'nullable|exists:jurusan,id';
            $rules['prodi_id'] = 'nullable|exists:prodi,id';
            $rules['username'] = 'nullable|string|max:50|unique:users,username,' . $user->id;
            $rules['password'] = 'nullable|min:6|confirmed';
        } elseif ($request->role === 'super_admin' || $request->role === 'admin') {
            $rules['username'] = 'required|string|max:50|unique:users,username,' . $user->id;
            $rules['password'] = 'nullable|min:6|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'tanggal_lahir' => $request->tanggal_lahir,
            'is_active' => $request->boolean('is_active', true),
        ];

        // Update berdasarkan role
        if ($request->role === 'super_admin' || $request->role === 'admin') {
            $userData['username'] = $request->username;
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            // Reset field dosen/mahasiswa
            $userData['nidn'] = null;
            $userData['nim'] = null;
            $userData['kelas'] = null;
            $userData['jurusan'] = null;
            $userData['prodi_id'] = null;
        } elseif ($request->role === 'dosen') {
            $userData['nidn'] = $request->nidn;
            $userData['username'] = $request->username ?? $request->nidn;
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $userData['jurusan'] = $request->jurusan;
            $userData['prodi_id'] = $request->prodi_id;
            // Reset field mahasiswa
            $userData['nim'] = null;
            $userData['kelas'] = null;
        } elseif ($request->role === 'mahasiswa') {
            $userData['nim'] = $request->nim;
            $userData['kelas'] = $request->kelas;
            $userData['username'] = $request->username ?? $request->nim;
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $userData['jurusan'] = $request->jurusan;
            $userData['prodi_id'] = $request->prodi_id;
            // Reset field dosen
            $userData['nidn'] = null;
        }

        $user->update($userData);

        return response()->json(['success' => true, 'message' => 'User berhasil diperbarui']);
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        // Cek apakah user super_admin terakhir
        if ($user->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus super_admin terakhir'], 422);
        }

        // Cek jika menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri'], 422);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'User berhasil dihapus']);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(User $user)
    {
        // Cek jika user super_admin dan sedang mencoba menonaktifkan diri sendiri
        if ($user->role === 'super_admin' && $user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menonaktifkan akun sendiri'
            ], 422);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Status user {$user->name} berhasil diubah.",
            'is_active' => $user->is_active
        ]);
    }

    /**
     * Get prodi berdasarkan jurusan (untuk AJAX cascading dropdown)
     */
    public function getProdiByJurusan($jurusan_id)
    {
        $prodi = Prodi::where('jurusan_id', $jurusan_id)
            ->where('is_active', true)
            ->orderBy('nama_prodi')
            ->get(['id', 'nama_prodi', 'jenjang']);

        return response()->json($prodi);
    }
}