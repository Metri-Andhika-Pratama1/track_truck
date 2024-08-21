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
    // Metode untuk menampilkan daftar perjalanan
    public function index()
    {
        $perjalanans = Perjalanan::paginate(10);
        return view('perjalanan.index', compact('perjalanans'));
    }

    // Metode untuk menampilkan form pembuatan perjalanan
    public function create()
    {
        $supirs = Supir::all();
        $truks = Truk::all();
        $gudangs = Gudang::all();
        return view('perjalanan.create', compact('supirs', 'truks', 'gudangs'));
    }

    // Metode untuk menyimpan data perjalanan baru
    public function store(Request $request)
    {
        $request->validate([
            'supir_id' => 'required|exists:supirs,id',
            'truk_id' => 'required|exists:truks,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'lat_berangkat' => 'required|numeric',
            'lng_berangkat' => 'required|numeric',
            'lat_tujuan' => 'required|numeric',
            'lng_tujuan' => 'required|numeric',
            'bensin_awal' => 'required|numeric',
            'bensin_akhir' => 'required|numeric',
        ]);

        Perjalanan::create($request->only([
            'supir_id', 'truk_id', 'gudang_id', 'lat_berangkat', 'lng_berangkat', 'lat_tujuan', 'lng_tujuan', 'bensin_awal', 'bensin_akhir'
        ]));

        return redirect()->route('perjalanan.index')->with('success', 'Perjalanan berhasil ditambahkan.');
    }

    // Metode untuk menampilkan detail perjalanan
    public function show($id)
    {
        $perjalanan = Perjalanan::findOrFail($id);

        // Ambil data bahan bakar terakhir dari detail perjalanan
        $latestDetail = $perjalanan->detail_perjalanans()->latest()->first();
        $lat_berangkat = $latestDetail ? $latestDetail->lat : $perjalanan->lat_berangkat;
        $lng_berangkat = $latestDetail ? $latestDetail->lng : $perjalanan->lng_berangkat;
        $bensin_akhir = $latestDetail ? $latestDetail->minyak : 'Tidak Ada';

        return view('perjalanan.show', [
            'perjalanan' => $perjalanan,
            'bensin_akhir' => $bensin_akhir,
            'lat_awal' => $lat_berangkat,
            'lng_awal' => $lng_berangkat,
            'lat_akhir' => $perjalanan->lat_tujuan,
            'lng_akhir' => $perjalanan->lng_tujuan,
        ]);
    }

    // Metode untuk menampilkan form edit perjalanan
    public function edit($id)
    {
        $perjalanan = Perjalanan::findOrFail($id);
        $supirs = Supir::all();
        $truks = Truk::all();
        $gudangs = Gudang::all();
        return view('perjalanan.edit', compact('perjalanan', 'supirs', 'truks', 'gudangs'));
    }

    // Metode untuk memperbarui data perjalanan
    public function update(Request $request, $id)
    {
        $request->validate([
            'supir_id' => 'required|exists:supirs,id',
            'truk_id' => 'required|exists:truks,id',
            'gudang_id' => 'required|exists:gudangs,id',
            'lat_berangkat' => 'required|numeric',
            'lng_berangkat' => 'required|numeric',
            'lat_tujuan' => 'required|numeric',
            'lng_tujuan' => 'required|numeric',
            'bensin_awal' => 'required|numeric',
            'bensin_akhir' => 'required|numeric',
        ]);

        $perjalanan = Perjalanan::findOrFail($id);
        $perjalanan->update($request->only([
            'supir_id', 'truk_id', 'gudang_id', 'lat_berangkat', 'lng_berangkat', 'lat_tujuan', 'lng_tujuan', 'bensin_awal', 'bensin_akhir'
        ]));

        return redirect()->route('perjalanan.index')->with('success', 'Perjalanan berhasil diperbarui.');
    }

    // Metode untuk menghapus data perjalanan
    public function destroy($id)
    {
        $perjalanan = Perjalanan::findOrFail($id);
        $perjalanan->delete();

        return redirect()->route('perjalanan.index')->with('success', 'Perjalanan berhasil dihapus.');
    }

    // Metode untuk mendapatkan data terbaru dari DetailPerjalanan
    public function getRealTimeData($id)
    {
        try {
            $perjalanan = Perjalanan::findOrFail($id);

            $latestDetail = DetailPerjalanan::where('perjalanan_id', $id)
                ->orderBy('id', 'desc')
                ->first();

            if ($latestDetail) {
                return response()->json([
                    'perjalanan_id' => $perjalanan->id,
                    'nama_supir' => $perjalanan->supir->nama ?? 'Tidak Ada',
                    'plat_nomor_truk' => $perjalanan->truk->plat_no ?? 'Tidak Ada',
                    'nama_gudang' => $perjalanan->gudang->nama_gudang ?? 'Tidak Ada',
                    'titik_berangkat' => [
                        'lat' => $latestDetail->lat,
                        'lng' => $latestDetail->lng
                    ],
                    'titik_tujuan' => [
                        'lat' => $perjalanan->lat_tujuan,
                        'lng' => $perjalanan->lng_tujuan
                    ],
                    'detail_perjalanan' => [
                        'lat' => $latestDetail->lat,
                        'lng' => $latestDetail->lng,
                        'persentase_bahan_bakar' => $latestDetail->minyak,
                        'timestamp' => $latestDetail->created_at
                    ]
                ]);
            }

            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Metode untuk memperbarui data real-time perjalanan
    public function updateRealTimeData(Request $request, $id)
    {
        try {
            // Validasi data dari permintaan
            $validated = $request->validate([
                'lat' => 'required|numeric',
                'lng' => 'required|numeric',
                'minyak' => 'required|numeric',
            ]);

            // Temukan perjalanan berdasarkan ID
            $perjalanan = Perjalanan::findOrFail($id);

            // Ambil detail perjalanan terbaru dan perbarui data
            $latestDetail = DetailPerjalanan::where('perjalanan_id', $id)
                ->orderBy('id', 'desc')
                ->first();

            if ($latestDetail) {
                $latestDetail->update($validated);

                return response()->json([
                    'success' => true,
                    'message' => 'Data perjalanan berhasil diperbarui.',
                    'detail_perjalanan' => $latestDetail
                ]);
            }

            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
