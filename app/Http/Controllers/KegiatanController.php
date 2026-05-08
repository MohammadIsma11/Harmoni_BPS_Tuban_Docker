<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kegiatan;
use App\Models\Team;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::with('team')->orderBy('created_at', 'desc')->paginate(15);
        return view('manajemen.kegiatan.index', compact('kegiatans'));
    }

    public function create()
    {
        $teams = Team::orderBy('nama_tim', 'asc')->get();
        return view('manajemen.kegiatan.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:150',
            'tim_id' => 'required|exists:teams,id',
            'min_honor_standard' => 'required|numeric',
            'max_honor_standard' => 'required|numeric',
        ]);

        Kegiatan::create($request->all());
        return redirect()->route('manajemen.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $teams = Team::orderBy('nama_tim', 'asc')->get();
        return view('manajemen.kegiatan.edit', compact('kegiatan', 'teams'));
    }

    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $request->validate([
            'nama_kegiatan' => 'required|string|max:150',
            'tim_id' => 'required|exists:teams,id',
            'min_honor_standard' => 'required|numeric',
            'max_honor_standard' => 'required|numeric',
        ]);

        $kegiatan->update($request->all());
        return redirect()->route('manajemen.kegiatan.index')->with('success', 'Data kegiatan diperbarui.');
    }

    public function destroy($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();
        return back()->with('success', 'Kegiatan berhasil dihapus.');
    }
}
