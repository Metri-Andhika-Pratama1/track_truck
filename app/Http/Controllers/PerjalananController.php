<?php

namespace App\Http\Controllers;

use App\Models\DetailPerjalanan;
use App\Models\Perjalanan;
use App\Models\Supir;
use App\Models\Truk;
use App\Models\Gudang;
use Illuminate\Http\Request;

class PerjalananController extends Controller
{
    /**
     * Menampilkan daftar semua Perjalanan.
     */
    public function index()
    {
        $perjalanans = Perjalanan::with(['supir', 'truk', 'gudang'])->paginate(10);
        
        return view('perjalanan.index', compact('perjalanans'));
    }

    /**
     * Menampilkan form untuk membuat Perjalanan baru.
     */
    public function create()
    {
        $supirs = Supir::all(); // Mengambil data supir
        $truks = Truk::all(); // Mengambil data truk
        $gudangs = Gudang::all(); // Mengambil data gudang
        return view('perjalanan.create', compact('supirs', 'truks', 'gudangs'));
    }

    /**
     * Menyimpan Perjalanan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supir_id' => 'required|exists:supirs,id',
            'truk_id' => 'required|exists:truks,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'lat_berangkat' => 'required|numeric|between:-90,90',
            'lng_berangkat' => 'required|numeric|between:-180,180',
            'lat_tujuan' => 'required|numeric|between:-90,90',
            'lng_tujuan' => 'required|numeric|between:-180,180',
            'bensin_awal' => 'required|numeric|min:0|max:100', // Pastikan dalam persen
            'bensin_akhir' => 'required|numeric|min:0|max:100', // Pastikan dalam persen
        ]);

        Perjalanan::create($request->only([
            'supir_id', 'truk_id', 'gudang_id', 'lat_berangkat', 'lng_berangkat',
            'lat_tujuan', 'lng_tujuan', 'bensin_awal', 'bensin_akhir'
        ]));

        return redirect()->route('perjalanan.index')->with('success', 'Perjalanan berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail dari Perjalanan yang spesifik.
     */
    public function show($id)
{
    $perjalanan = Perjalanan::with(['supir', 'truk', 'gudang', 'details'])->findOrFail($id);

    // Mengelompokkan detail perjalanan untuk menampilkan data awal dan akhir
    $details = $perjalanan->details->groupBy('perjalanan_id')->map(function ($group) {
        $first = $group->first(); // Data awal
        $last = $group->last();   // Data akhir
        return [
            'supir_nama' => $first->perjalanan->supir->nama ?? 'Tidak Ada',
            'plat_no' => $first->perjalanan->truk->plat_no ?? 'Tidak Ada',
            'lat_awal' => $first->lat,
            'lng_awal' => $first->lng,
            'lat_akhir' => $last->lat,
            'lng_akhir' => $last->lng,
            'minyak_awal' => $first->minyak,
            'minyak_akhir' => $last->minyak,
        ];
    });

    // Mengambil data perjalanan untuk grafik
    $perjalanans = Perjalanan::select('id', 'bensin_awal', 'bensin_akhir')->get();
    $labels = $perjalanans->pluck('id');
    $bensinAwal = $perjalanans->pluck('bensin_awal');
    $bensinAkhir = $perjalanans->pluck('bensin_akhir');

    return view('perjalanan.show', [
        'perjalanan' => $perjalanan,
        'details' => $details,
        'labels' => $labels,
        'bensinAwal' => $bensinAwal,
        'bensinAkhir' => $bensinAkhir
    ]);
}

        /**
     * Menampilkan grafik bensin awal dan akhir.
     */
    public function grafik()
    {
        $perjalanan = Perjalanan::select('id', 'bensin_awal', 'bensin_akhir')->get();
        
        $labels = $perjalanan->pluck('id');
        $bensinAwal = $perjalanan->pluck('bensin_awal');
        $bensinAkhir = $perjalanan->pluck('bensin_akhir');

        return view('dashboard', [
            'labels' => $labels,
            'bensinAwal' => $bensinAwal,
            'bensinAkhir' => $bensinAkhir,
        ]);
    }

    /**
     * Menampilkan form untuk mengedit Perjalanan yang spesifik.
     */
    public function edit($id)
{
    $perjalanan = Perjalanan::findOrFail($id);
    $supirs = Supir::all();
    $truks = Truk::all();
    $gudangs = Gudang::all();
    
    // Mengambil data minyak yang terkait dengan perjalanan
    $minyak = DetailPerjalanan::where('perjalanan_id', $id)->first(); // Sesuaikan dengan relasi yang Anda punya
    
    return view('perjalanan.edit', compact('perjalanan', 'supirs', 'truks', 'gudangs', 'minyak'));
}


    /**
     * Memperbarui Perjalanan yang spesifik di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'supir_id' => 'required|exists:supirs,id',
            'truk_id' => 'required|exists:truks,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'lat_berangkat' => 'required|numeric|between:-90,90',
            'lng_berangkat' => 'required|numeric|between:-180,180',
            'lat_tujuan' => 'required|numeric|between:-90,90',
            'lng_tujuan' => 'required|numeric|between:-180,180',
            'bensin_awal' => 'required|numeric|min:0|max:100', // Pastikan dalam persen
            'bensin_akhir' => 'required|numeric|min:0|max:100', // Pastikan dalam persen
        ]);

        $perjalanan = Perjalanan::findOrFail($id);
        $perjalanan->update($request->only([
            'supir_id', 'truk_id', 'gudang_id', 'lat_berangkat', 'lng_berangkat',
            'lat_tujuan', 'lng_tujuan', 'bensin_awal', 'bensin_akhir'
        ]));

        return redirect()->route('perjalanan.index')->with('success', 'Perjalanan berhasil diperbarui.');
    }

    /**
     * Menghapus Perjalanan yang spesifik dari database.
     */
    public function destroy($id)
    {
        $perjalanan = Perjalanan::findOrFail($id);
        $perjalanan->delete();

        return redirect()->route('perjalanan.index')->with('success', 'Perjalanan berhasil dihapus.');
    }

    public function printPerjalanan($id)
    {
        $detail = DetailPerjalanan::with(['perjalanan.supir', 'perjalanan.truk', 'perjalanan.gudang'])
            ->where('id', $id)
            ->firstOrFail();

        return view('cetak.print_perjalanan', compact('detail'));
    }
}
