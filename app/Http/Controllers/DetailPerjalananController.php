<?php

namespace App\Http\Controllers;

use App\Models\DetailPerjalanan;
use App\Models\Perjalanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class DetailPerjalananController extends Controller
{
    public function index()
    {
        // Mengambil ID perjalanan yang unik
        $perjalananIds = DetailPerjalanan::pluck('perjalanan_id')->unique();

        // Mendapatkan data pertama dan terakhir untuk setiap perjalanan_id
        $details = collect();
        foreach ($perjalananIds as $perjalananId) {
            $first = DetailPerjalanan::where('perjalanan_id', $perjalananId)
                ->orderBy('id', 'asc') // Menggunakan ID sebagai pengganti timestamp
                ->first();
                
            $last = DetailPerjalanan::where('perjalanan_id', $perjalananId)
                ->orderBy('id', 'desc') // Menggunakan ID sebagai pengganti timestamp
                ->first();
                
            if ($first) {
                $details->push($first);
            }
            if ($last && $last->id !== $first->id) {
                $details->push($last);
            }
        }
        
        // Paginate the results
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Number of items per page
        $currentItems = $details->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $details = new LengthAwarePaginator($currentItems, $details->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);
        
        return view('detail_perjalanan.index', compact('details'));
    }

    public function create()
    {
        $perjalanans = Perjalanan::all(); // Mengambil data perjalanan
        return view('detail_perjalanan.create', compact('perjalanans'));
    }

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

    public function show($id)
    {
        $detail = DetailPerjalanan::with('perjalanan')->findOrFail($id);
        return view('detail_perjalanan.show', compact('detail'));
    }

    public function edit($id)
    {
        $detail = DetailPerjalanan::findOrFail($id);
        $perjalanans = Perjalanan::all();
        return view('detail_perjalanan.edit', compact('detail', 'perjalanans'));
    }

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

    public function destroy($id)
    {
        $detail = DetailPerjalanan::findOrFail($id);
        $detail->delete();

        return redirect()->route('details.index')->with('success', 'Detail perjalanan berhasil dihapus.');
    }

    public function getLatestByPerjalananId($perjalananId)
    {
        try {
            $latestDetail = DetailPerjalanan::where('perjalanan_id', $perjalananId)
                                            ->orderBy('id', 'desc') // Menggunakan ID sebagai pengganti timestamp
                                            ->first();

            if ($latestDetail) {
                return response()->json([
                    'lat' => $latestDetail->lat,
                    'lng' => $latestDetail->lng,
                    'persentase_bahan_bakar' => $latestDetail->minyak,
                ]);
            }

            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
