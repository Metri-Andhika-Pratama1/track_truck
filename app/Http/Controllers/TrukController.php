<?php

namespace App\Http\Controllers;

use App\Models\Truk;
use Illuminate\Http\Request;

class TrukController extends Controller
{
    /**
     * Menampilkan daftar semua Truk.
     */
    public function index()
    {
        $truks = Truk::paginate(10);
        return view('truk.index', compact('truks'));
    }

    /**
     * Menampilkan form untuk membuat Truk baru.
     */
    public function create()
    {
        return view('truk.create');
    }

    /**
     * Menyimpan Truk baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plat_no' => 'required|string|max:50',
            'manufaktur' => 'required|string|max:255',
            'seri' => 'required|string|max:255',
            'tahun_pembuatan' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        Truk::create($request->all());

        return redirect()->route('truk.index')->with('success', 'Truk berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail dari Truk yang spesifik.
     */
    public function show($id)
    {
        $truk = Truk::findOrFail($id);
        return view('truk.show', compact('truk'));
    }

    /**
     * Menampilkan form untuk mengedit Truk yang spesifik.
     */
    public function edit($id)
    {
        $truk = Truk::findOrFail($id);
        return view('truk.edit', compact('truk'));
    }

    /**
     * Memperbarui Truk yang spesifik di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'plat_no' => 'required|string|max:50',
            'manufaktur' => 'required|string|max:255',
            'seri' => 'required|string|max:255',
            'tahun_pembuatan' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        $truk = Truk::findOrFail($id);
        $truk->update($request->all());

        return redirect()->route('truk.index')->with('success', 'Truk berhasil diperbarui.');
    }

    /**
     * Menghapus Truk yang spesifik dari database.
     */
    public function destroy($id)
    {
        $truk = Truk::findOrFail($id);
        $truk->delete();

        return redirect()->route('truk.index')->with('success', 'Truk berhasil dihapus.');
    }
}
