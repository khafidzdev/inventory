<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users (excluding super_admin).
     */
    public function index(): View
    {
        $users = User::where('role', '!=', 'super_admin')
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = [
            'warehouse_staff' => 'Staff Gudang',
            'cashier' => 'Kasir',
        ];

        return view('dashboard.Admin.User', compact('users', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:warehouse_staff,cashier'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'in:0,1'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Pilihan role hanya Staff Gudang atau Kasir.',
            'is_active.required' => 'Status akun wajib dipilih.',
            'is_active.in' => 'Pilihan status akun tidak valid.',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Proteksi: jangan izinkan mengubah akun super_admin dari menu ini
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Akun Super Admin tidak dapat diubah melalui menu ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:warehouse_staff,cashier'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'in:0,1'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Pilihan role hanya Staff Gudang atau Kasir.',
            'is_active.required' => 'Status akun wajib dipilih.',
            'is_active.in' => 'Pilihan status akun tidak valid.',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active');

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Akun Super Admin tidak dapat dihapus.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users')->with('success', "User {$userName} berhasil dihapus.");
    }
}
