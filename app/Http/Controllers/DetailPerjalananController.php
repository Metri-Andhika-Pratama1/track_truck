<?php

namespace App\Http\Controllers;

use App\Models\DetailPerjalanan;
use App\Models\Perjalanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DetailPerjalananController extends Controller
{
    /**
     * Menampilkan daftar semua DetailPerjalanan.
     */
    public function index()
    {
      
        // Tambahkan pagination
         $details = DetailPerjalanan::with('perjalanan')->paginate(10); // 10 data per halaman
        return view('detail_perjalanan.index', compact('details'));

    }

    /**
     * Menampilkan form untuk membuat DetailPerjalanan baru.
     */
    public function create()
    {
        $perjalanans = Perjalanan::all(); // Mengambil data perjalanan
        return view('detail_perjalanan.create', compact('perjalanans'));
    }

    /**
     * Menyimpan DetailPerjalanan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'perjalanan_id' => 'required|exists:perjalanans,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'minyak' => 'required|numeric',
        ]);

        DetailPerjalanan::create($request->only([
            'perjalanan_id', 'lat', 'lng', 'minyak'
        ]));

        return redirect()->route('details.index')->with('success', 'Detail perjalanan berhasil ditambahkan.');
    }

    /**
     * Endpoint untuk menerima data dari sensor di Arduino.
     */
    public function receiveFromSensor(Request $request)
{
    try {
        $perjalananId = $request->input('perjalanan_id');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $persentaseBahanBakar = $request->input('persentase_bahan_bakar');
        $timestamp = $request->input('timestamp') ?? now()->toDateTimeString();  // Gunakan waktu server jika tidak tersedia

        // Validasi data
        $request->validate([
            'perjalanan_id' => 'required|exists:perjalanans,id',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'persentase_bahan_bakar' => 'nullable|numeric|min:0|max:100',
            'timestamp' => 'nullable|string',
        ]);

        // Cache data sementara berdasarkan perjalanan_id
        $cacheKey = 'sensor_data_' . $perjalananId;
        $cachedData = Cache::get($cacheKey, []);

        if ($lat !== null && $lng !== null) {
            // Simpan data GPS sementara
            $cachedData['lat'] = $lat;
            $cachedData['lng'] = $lng;
        }

        if ($persentaseBahanBakar !== null) {
            // Simpan data bahan bakar sementara
            $cachedData['persentase_bahan_bakar'] = $persentaseBahanBakar;
        }

        // Perbarui cache dengan data terbaru
        Cache::put($cacheKey, $cachedData, now()->addMinutes(5));

        // Jika semua data telah terkumpul, simpan ke database
        if (isset($cachedData['lat']) && isset($cachedData['lng']) && isset($cachedData['persentase_bahan_bakar'])) {
            DetailPerjalanan::create([
                'perjalanan_id' => $perjalananId,
                'lat' => $cachedData['lat'],
                'lng' => $cachedData['lng'],
                'minyak' => $cachedData['persentase_bahan_bakar'],
                'timestamp' => $timestamp
            ]);

            // Hapus cache setelah data disimpan
            Cache::forget($cacheKey);

            Log::info('Data sensor berhasil disimpan:', $cachedData);

            return response()->json(['message' => 'Data berhasil disimpan'], 201);
        } else {
            return response()->json(['message' => 'Data belum lengkap, menunggu data lain'], 202);
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Data sensor gagal divalidasi:', ['errors' => $e->errors()]);
        return response()->json(['error' => 'Data gagal divalidasi', 'details' => $e->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Data sensor gagal disimpan:', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Data gagal disimpan: ' . $e->getMessage()], 500);
    }
}


    /**
     * Menampilkan detail dari DetailPerjalanan yang spesifik.
     */
    public function show($id)
    {
        $detail = DetailPerjalanan::with('perjalanan')->findOrFail($id);
        return view('detail_perjalanan.show', compact('detail'));
    }

    /**
     * Menampilkan form untuk mengedit DetailPerjalanan yang spesifik.
     */
    public function edit($id)
    {
        $detail = DetailPerjalanan::findOrFail($id);
        $perjalanans = Perjalanan::all();
        return view('detail_perjalanan.edit', compact('detail', 'perjalanans'));
    }

    /**
     * Memperbarui DetailPerjalanan yang spesifik di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'perjalanan_id' => 'required|exists:perjalanans,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'minyak' => 'required|numeric',
        ]);

        $detail = DetailPerjalanan::findOrFail($id);
        $detail->update($request->only([
            'perjalanan_id', 'lat', 'lng', 'minyak'
        ]));

        return redirect()->route('details.index')->with('success', 'Detail perjalanan berhasil diperbarui.');
    }

    /**
     * Menghapus DetailPerjalanan yang spesifik dari database.
     */
    public function destroy($id)
    {
        $detail = DetailPerjalanan::findOrFail($id);
        $detail->delete();

        return redirect()->route('details.index')->with('success', 'Detail perjalanan berhasil dihapus.');
    }

    /**
     * Mengambil nilai persentase bahan bakar terbaru berdasarkan perjalanan_id.
     */
    public function getLatestFuelLevel($perjalananId)
    {
        try {
            $latestDetail = DetailPerjalanan::where('perjalanan_id', $perjalananId)
                                            ->orderBy('timestamp', 'desc')
                                            ->first();

            if ($latestDetail) {
                return response()->json([
                    'persentase_bahan_bakar' => $latestDetail->minyak
                ]);
            }

            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
