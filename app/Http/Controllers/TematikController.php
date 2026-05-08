<?php

namespace App\Http\Controllers;

use App\Models\Tematik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TematikController extends Controller
{
    public function index()
    {
        return view('tematik.index');
    }

    public function getData()
    {
        $locations = Tematik::orderBy('id', 'asc')->get();
        return response()->json($locations);
    }

    public function getLaporan()
    {
        $laporan = Tematik::orderBy('tanggal', 'desc')->get();
        return response()->json($laporan);
    }

    public function getInfo()
    {
        $info = Tematik::selectRaw('kecamatan as kategori, count(*) as jumlah')
            ->groupBy('kecamatan')
            ->orderBy('jumlah', 'desc')
            ->get()
            ->map(function($item, $index) {
                return [
                    'id' => $index + 1,
                    'kategori' => $item->kategori ?: 'Lainnya',
                    'jumlah' => $item->jumlah,
                    'keterangan' => 'Total titik lokasi terdaftar'
                ];
            });
        return response()->json($info);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
            'sls' => 'nullable|string|max:200',
            'judul' => 'required|string|max:200',
            'member' => 'nullable|string',
            'status' => 'required|string|max:50',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'pic' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        $tematik = Tematik::create($validated);
        return response()->json($tematik, 201);
    }

    public function update(Request $request, $id)
    {
        $tematik = Tematik::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'judul' => 'required|string|max:200',
            'member' => 'nullable|string',
            'status' => 'required|string|max:50',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
            'sls' => 'nullable|string|max:200',
        ]);

        $tematik->update($validated);
        return response()->json($tematik);
    }

    public function getUsers()
    {
        $users = \App\Models\User::whereIn('role', ['Pegawai', 'Katim', 'Kepala', 'Admin'])
            ->select('id', 'nama_lengkap')
            ->orderBy('nama_lengkap', 'asc')
            ->get();
        return response()->json($users);
    }

    public function destroy($id)
    {
        $tematik = Tematik::findOrFail($id);
        $tematik->delete();
        return response()->json(['success' => true]);
    }
}
