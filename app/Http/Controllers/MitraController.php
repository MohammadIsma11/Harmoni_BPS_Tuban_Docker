<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mitra;
use App\Models\User;
use App\Imports\MitraImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;

class MitraController extends Controller
{
    public function index()
    {
        $mitras = Mitra::orderBy('nama_lengkap', 'asc')->paginate(15);
        return view('manajemen.mitra.index', compact('mitras'));
    }

    public function create()
    {
        return view('manajemen.mitra.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sobat_id' => 'required|unique:m_mitra,sobat_id',
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'nullable|email|max:100',
            'no_telp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
            'max_honor_bulanan' => 'required|numeric',
            'alamat_prov' => 'nullable|string|max:100',
            'alamat_kab' => 'nullable|string|max:100',
            'alamat_kec' => 'nullable|string|max:100',
            'alamat_desa' => 'nullable|string|max:100',
            'posisi_daftar' => 'nullable|string|max:100',
        ]);

        $data = $request->all();
        // Convert posisi array to string
        if (isset($data['posisi']) && is_array($data['posisi'])) {
            $data['posisi'] = implode(', ', $data['posisi']);
        }

        $mitra = Mitra::create($data);

        // Create/Update User Account for Mitra
        $user = User::where('username', $mitra->sobat_id)->first();
        if (!$user) {
            User::create([
                'username' => $mitra->sobat_id,
                'nama_lengkap' => $mitra->nama_lengkap,
                'email'        => $mitra->email,
                'password'     => Hash::make('sobat123'),
                'role'         => 'Mitra',
            ]);
        } else {
            $user->update([
                'nama_lengkap' => $mitra->nama_lengkap,
                'email'        => $mitra->email,
                'role'         => 'Mitra',
            ]);
        }

        return redirect()->route('manajemen.mitra.index')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $mitra = Mitra::findOrFail($id);
        return view('manajemen.mitra.edit', compact('mitra'));
    }

    public function update(Request $request, $id)
    {
        $mitra = Mitra::findOrFail($id);
        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'nullable|email|max:100',
            'no_telp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
            'max_honor_bulanan' => 'required|numeric',
            'alamat_prov' => 'nullable|string|max:100',
            'alamat_kab' => 'nullable|string|max:100',
            'alamat_kec' => 'nullable|string|max:100',
            'alamat_desa' => 'nullable|string|max:100',
            'posisi_daftar' => 'nullable|string|max:100',
        ]);

        $data = $request->all();
        // Convert posisi array to string
        if (isset($data['posisi']) && is_array($data['posisi'])) {
            $data['posisi'] = implode(', ', $data['posisi']);
        } else {
            $data['posisi'] = null;
        }

        $mitra->update($data);

        // Sync with Users
        User::where('username', $mitra->sobat_id)->update([
            'nama_lengkap' => $mitra->nama_lengkap,
            'email'        => $mitra->email,
            'role'         => 'Mitra',
        ]);

        return redirect()->route('manajemen.mitra.index')->with('success', 'Data mitra diperbarui.');
    }

    public function destroy($id)
    {
        $mitra = Mitra::findOrFail($id);
        
        // Remove User Account
        User::where('username', $mitra->sobat_id)->delete();
        
        $mitra->delete();
        return back()->with('success', 'Mitra berhasil dihapus.');
    }

    public function truncate()
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                // Delete associated users first
                $sobatIds = Mitra::pluck('sobat_id');
                User::whereIn('username', $sobatIds)->where('role', 'Mitra')->delete();
                
                // Truncate mitra (using delete to trigger any observers if any, though truncate is faster)
                Mitra::whereIn('sobat_id', $sobatIds)->delete();
            });

            return redirect()->route('manajemen.mitra.index')->with('success', 'Seluruh data mitra dan akun terkait berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        set_time_limit(0); // Mencegah timeout untuk data besar
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MitraImport, $request->file('file_excel'));
            return redirect()->route('manajemen.mitra.index')->with('success', 'Data mitra berhasil diimport dari Excel.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
