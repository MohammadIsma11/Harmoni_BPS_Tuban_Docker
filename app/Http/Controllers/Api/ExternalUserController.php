<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ExternalUserController extends Controller
{
    /**
     * Get all users for external integration (Ticketing)
     */
    public function index()
    {
        // Fetch all users with basic info needed for ticketing
        $users = User::select('id', 'nama_lengkap', 'nip', 'username', 'role', 'email', 'team_id')
            ->with(['team' => function($query) {
                $query->select('id', 'nama_tim');
            }])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }
}
