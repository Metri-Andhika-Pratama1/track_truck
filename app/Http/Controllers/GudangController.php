<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;


class GudangController extends Controller
{
    /**
     * Menampilkan daftar semua Gudang.
     */
    public function index()
    {
        
        // Mengambil semua Gudang dengan pagination
        $gudangs = Gudang::paginate(10); // Ganti angka 10 dengan jumlah item per halaman sesuai kebutuhan
        
        return view('gudang.index', compact('gudangs'));
    
        
    }

    /**
     * Menampilkan form untuk membuat Gudang baru.
     */
    public function create()
    {
        return view('gudang.create');
    }

    /**
     * Menyimpan Gudang baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        Gudang::create($request->all());

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail dari Gudang yang spesifik.
     */
    public function show($id)
    {
        $gudang = Gudang::findOrFail($id);
        return view('gudang.show', compact('gudang'));
    }

    /**
     * Menampilkan form untuk mengedit Gudang yang spesifik.
     */
    public function edit($id)
    {
        $gudang = Gudang::findOrFail($id);
        return view('gudang.edit', compact('gudang'));
    }

    /**
     * Memperbarui Gudang yang spesifik di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $gudang = Gudang::findOrFail($id);
        $gudang->update($request->all());

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    /**
     * Menghapus Gudang yang spesifik dari database.
     */
    public function destroy($id)
    {
        $gudang = Gudang::findOrFail($id);
        $gudang->delete();

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil dihapus.');
    }
}
