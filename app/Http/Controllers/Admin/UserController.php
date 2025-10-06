<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user (admin only).
     */
    public function index()
    {
        $this->authorize('users.manage');

        $users = User::select('id', 'username', 'role', 'created_at', 'updated_at')
            ->orderBy('username')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Tambah user baru (admin only).
     */
    public function store(Request $request)
    {
        $this->authorize('users.manage');

        $data = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', Rule::in(['admin', 'user'])],
        ]);

        User::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        return back()->with('status', 'User berhasil ditambahkan.');
    }

    /**
     * Update user (admin only).
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('users.manage');

        $data = $request->validate([
            'username' => [
                'required', 'string', 'max:255',
                Rule::unique('users', 'username')->ignore($user->getKey()),
            ],
            'role'     => ['required', Rule::in(['admin', 'user'])],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $user->username = $data['username'];
        $user->role     = $data['role'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('status', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user (admin only).
     */
    public function destroy(Request $request, User $user)
    {
        $this->authorize('users.manage');

        // Cegah menghapus akun sendiri
        if ($request->user()->getKey() === $user->getKey()) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $user->delete();

        return back()->with('status', 'User dihapus.');
    }
}
