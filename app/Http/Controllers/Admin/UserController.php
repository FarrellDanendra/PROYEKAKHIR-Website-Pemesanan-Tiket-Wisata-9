<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('roles')->latest();

        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->where(function ($q) use ($cari) {
                $q->where('name', 'like', "%{$cari}%")
                  ->orWhere('email', 'like', "%{$cari}%");
            });
        }

        if ($request->filled('role')) {
            $role = $request->input('role');
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = ['admin', 'pengelola', 'petugas', 'wisatawan'];

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $validated = $request->validate([
            'role' => ['required', 'in:admin,pengelola,petugas,wisatawan'],
        ]);

        $user->update(['role' => $validated['role']]);
        $user->syncRoles([$validated['role']]);

        return back()->with('success', "Role {$user->name} berhasil diubah menjadi {$validated['role']}.");
    }

    public function toggleBlock(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat memblokir akun Anda sendiri.');
        }

        $user->update(['is_blocked' => ! $user->is_blocked]);
        $statusText = $user->is_blocked ? 'diblokir' : 'dibuka blokirnya';

        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }
}