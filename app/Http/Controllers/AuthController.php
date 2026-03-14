<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        // Jika sudah login, lempar ke halaman yang sesuai rolenya
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    // Proses login
    public function loginAction(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // 1. Regenerate session untuk keamanan
            $request->session()->regenerate();

            // 2. Redirect cerdas berdasarkan role (Mencegah 403 Forbidden)
            return $this->redirectBasedOnRole(Auth::user());
        }

        // Jika gagal
        return back()->with('error', 'Username atau Password salah!')->withInput();
    }

    /**
     * Helper function untuk menentukan arah redirect setelah login/check
     */
    private function redirectBasedOnRole($user)
    {
        if ($user->role === 'Admin') {
            // Admin IT langsung ke Manajemen User (Karena tidak punya dashboard)
            return redirect()->route('manajemen.anggota')->with('success', 'Selamat Datang, Admin IT!');
        }

        // Role lainnya (Kepala, Katim, Pegawai) ke Dashboard
        return redirect()->intended('dashboard')->with('success', 'Selamat Datang Kembali!');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil Keluar.');
    }
}