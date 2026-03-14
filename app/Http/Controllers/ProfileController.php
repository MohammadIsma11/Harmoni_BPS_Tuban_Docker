<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    // 1. Tentukan Role yang diizinkan (Validation Logic)
    $allowedRoles = [$user->role]; // Default: hanya boleh role dirinya sendiri

    if ($user->role === 'Kepala') {
        $allowedRoles = ['Kepala', 'Pegawai'];
    } elseif ($user->role === 'Katim') {
        $allowedRoles = ['Katim', 'Pegawai'];
    } 
    // Jika role === 'Pegawai', $allowedRoles tetap hanya ['Pegawai']

    $request->validate([
        'nama_lengkap' => 'required|string|max:255',
        'username'     => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        'role'         => ['required', Rule::in($allowedRoles)],
        'password'     => 'nullable|min:8|confirmed',
    ]);

    $user->nama_lengkap = $request->nama_lengkap;
    $user->username = $request->username;
    
    // 2. Update Role hanya jika user bukan Admin
    if ($user->role !== 'Admin') {
        $user->role = $request->role;
    }

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return back()->with('success', 'Profil berhasil diperbarui!');
}
}