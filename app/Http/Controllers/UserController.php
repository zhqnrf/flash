<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
// Tampilkan List dengan Pencarian, Sort, dan Paginasi
    public function index(Request $request)
    {
        $query = User::query();

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Fitur Pengurutan
        $sort = $request->input('sort', 'terbaru');
        if ($sort == 'az') {
            $query->orderBy('name', 'asc');
        } elseif ($sort == 'za') {
            $query->orderBy('name', 'desc');
        } elseif ($sort == 'terlama') {
            $query->orderBy('id', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        // Paginasi Manual yang Aman dari Error
        $users = $query->paginate(10)->appends($request->query());

        return view('users.index', compact('users'));
    }

    // Tampilkan Halaman Tambah
    public function create()
    {
        return view('users.create');
    }

    // Proses Simpan Data Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'Akun berhasil ditambahkan!');
    }

    // Tampilkan Halaman Edit
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // Proses Update Data
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6', // Password boleh kosong jika tidak diubah
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Jika password diisi, update passwordnya
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Akun berhasil diupdate!');
    }

    // Proses Hapus Data
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus!');
    }
}