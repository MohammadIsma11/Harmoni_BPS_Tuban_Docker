<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Switch dashboard mode
     */
    public function switchMode(Request $request)
    {
        $mode = $request->mode; // 'harmoni' or 'honor'
        
        if (in_array($mode, ['harmoni', 'honor'])) {
            session(['dashboard_mode' => $mode]);
        }
        
        return redirect()->route('dashboard')->with('success', 'Berhasil berpindah modul.');
    }
}
