<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SSOController extends Controller
{
    public function check(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $token = Str::random(60);
            
            // Simpan token di cache selama 1 menit untuk validasi di aplikasi Ticket
            Cache::put('sso_token_' . $token, [
                'id' => $user->id,
                'username' => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'role' => $user->role,
            ], 60);

            $redirectUrl = $request->query('redirect', 'http://localhost:8000');
            $separator = parse_url($redirectUrl, PHP_URL_QUERY) ? '&' : '?';
            
            return redirect($redirectUrl . $separator . 'sso_token=' . $token);
        }

        return redirect('http://localhost:8080/login');
    }

    public function validateToken(Request $request)
    {
        $token = $request->input('token');
        $userData = Cache::get('sso_token_' . $token);

        if ($userData) {
            Cache::forget('sso_token_' . $token);
            return response()->json([
                'success' => true,
                'user' => $userData
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token invalid or expired'
        ], 401);
    }
}
