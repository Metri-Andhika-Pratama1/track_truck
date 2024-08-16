<?php

namespace App\Http\Controllers;

use App\Models\Supir;
use Illuminate\Http\Request;

class SupirController extends Controller
{
    /**
     * Menampilkan daftar semua Supir.
     */
    public function index()
    {
        $supirs = Supir::paginate(10);
        return view('supir.index', compact('supirs'));
    }

    /**
     * Menampilkan form untuk membuat Supir baru.
     */
    public function create()
    {
        return view('supir.create');
    }

    /**
     * Menyimpan Supir baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_karyawan' => 'required|string|max:50',
            'noHP' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
        ]);

        // Membuat instance baru Supir dan menyimpan data ke database
        Supir::create([
            'nama' => $request->input('nama'),
            'no_karyawan' => $request->input('no_karyawan'),
            'noHP' => $request->input('noHP'),
            'alamat' => $request->input('alamat'),
        ]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('supir.index')->with('success', 'Supir berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail dari Supir yang spesifik.
     */
    public function show($id)
    {
        $supir = Supir::findOrFail($id);
        return view('supir.show', compact('supir'));
    }

    /**
     * Menampilkan form untuk mengedit Supir yang spesifik.
     */
    public function edit($id)
    {
        $supir = Supir::findOrFail($id);
        return view('supir.edit', compact('supir'));
    }

    /**
     * Memperbarui Supir yang spesifik di database.
     */
    public function update(Request $request, $id)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_karyawan' => 'required|string|max:50',
            'noHP' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
        ]);

        // Mengambil instance Supir yang akan diperbarui
        $supir = Supir::findOrFail($id);
        $supir->update([
            'nama' => $request->input('nama'),
            'no_karyawan' => $request->input('no_karyawan'),
            'noHP' => $request->input('noHP'),
            'alamat' => $request->input('alamat'),
        ]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('supir.index')->with('success', 'Supir berhasil diperbarui.');
    }

    /**
     * Menghapus Supir yang spesifik dari database.
     */
    public function destroy($id)
    {
        $supir = Supir::findOrFail($id);
        $supir->delete();

        return redirect()->route('supir.index')->with('success', 'Supir berhasil dihapus.');
    }
}
