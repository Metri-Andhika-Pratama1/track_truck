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
        // Retrieve all DetailPerjalanan records with pagination
        $details = DetailPerjalanan::with('perjalanan')->paginate(10); // 10 items per page

        return view('detail_perjalanan.index', compact('details'));
    }

    public function create()
    {
        $perjalanans = Perjalanan::all(); // Retrieve all Perjalanan records
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
            $timestamp = $request->input('timestamp') ?? now()->toDateTimeString(); // Use server time if not provided

            // Validate data
            $request->validate([
                'perjalanan_id' => 'required|exists:perjalanans,id',
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
                'persentase_bahan_bakar' => 'nullable|numeric|min:0|max:100',
                'timestamp' => 'nullable|string',
            ]);

            // Temporary cache data based on perjalanan_id
            $cacheKey = 'sensor_data_' . $perjalananId;
            $cachedData = Cache::get($cacheKey, []);

            if ($lat !== null && $lng !== null) {
                // Store GPS data temporarily
                $cachedData['lat'] = $lat;
                $cachedData['lng'] = $lng;
            }

            if ($persentaseBahanBakar !== null) {
                // Store fuel data temporarily
                $cachedData['persentase_bahan_bakar'] = $persentaseBahanBakar;
            }

            // Update cache with latest data
            Cache::put($cacheKey, $cachedData, now()->addMinutes(5));

            // Save to database if all data is collected
            if (isset($cachedData['lat']) && isset($cachedData['lng']) && isset($cachedData['persentase_bahan_bakar'])) {
                DetailPerjalanan::create([
                    'perjalanan_id' => $perjalananId,
                    'lat' => $cachedData['lat'],
                    'lng' => $cachedData['lng'],
                    'minyak' => $cachedData['persentase_bahan_bakar'],
                    'timestamp' => $timestamp
                ]);

                // Remove cache after saving
                Cache::forget($cacheKey);

                Log::info('Data sensor successfully saved:', $cachedData);

                return response()->json(['message' => 'Data successfully saved'], 201);
            } else {
                return response()->json(['message' => 'Data incomplete, waiting for other data'], 202);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Sensor data validation failed:', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Data validation failed', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to save sensor data:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to save data: ' . $e->getMessage()], 500);
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
    // Di dalam DetailPerjalananController atau PerjalananController
 /**
     * Get real-time location data for a specific Perjalanan
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRealTimeLocation($id)
    {
        // Retrieve the latest DetailPerjalanan for the specified perjalanan ID
        $detail = DetailPerjalanan::where('perjalanan_id', $id)->latest()->first();

        // Check if detail data exists
        if ($detail) {
            return response()->json([
                'latitude' => $detail->lat,
                'longitude' => $detail->lng,
            ]);
        }

        // If no data is found, return a default response
        return response()->json([
            'latitude' => null,
            'longitude' => null,
            'message' => 'Location data not available'
        ], 404);
    }

    /**
     * Get real-time fuel level for a specific Perjalanan
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRealTimeFuelLevel($id)
    {
        // Retrieve the latest DetailPerjalanan for the specified perjalanan ID
        $detail = DetailPerjalanan::where('perjalanan_id', $id)->latest()->first();

        // Check if detail data exists
        if ($detail) {
            return response()->json([
                'fuelLevel' => $detail->minyak,
            ]);
        }

        // If no data is found, return a default response
        return response()->json([
            'fuelLevel' => null,
            'message' => 'Fuel level data not available'
        ], 404);
    }


    public function getLatestByPerjalananId($perjalananId)
    {
        try {
            $latestDetail = DetailPerjalanan::where('perjalanan_id', $perjalananId)
                                            ->orderBy('id', 'desc') // Using ID as a substitute for timestamp
                                            ->first();

            if ($latestDetail) {
                return response()->json([
                    'lat' => $latestDetail->lat,
                    'lng' => $latestDetail->lng,
                    'persentase_bahan_bakar' => $latestDetail->minyak,
                ]);
            }

            return response()->json(['error' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
