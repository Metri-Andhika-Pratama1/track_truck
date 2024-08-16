<?php

namespace App\Http\Controllers;

use App\Models\Perjalanan;
use App\Models\DetailPerjalanan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data perjalanan secara berurutan berdasarkan waktu
        $perjalanans = Perjalanan::orderBy('created_at', 'asc')->get();

        // Persiapkan data untuk grafik
        $labels = $perjalanans->pluck('id')->toArray(); // ID perjalanan sebagai label
        $bensinAwal = $perjalanans->pluck('bensin_awal')->toArray(); // Bensin awal untuk setiap perjalanan
        $bensinAkhir = $perjalanans->pluck('bensin_akhir')->toArray(); // Bensin akhir untuk setiap perjalanan

        // Ambil data detail perjalanan awal dan akhir
        $details = DetailPerjalanan::with('perjalanan.supir', 'perjalanan.truk')
                                   ->orderBy('created_at', 'asc')
                                   ->get()
                                   ->groupBy('perjalanan_id')
                                   ->map(function ($group) {
                                       return [
                                           'first' => $group->first(),
                                           'last' => $group->last(),
                                       ];
                                   })
                                   ->values()
                                   ->toArray();

        // Kirim data ke view
        return view('dashboard', [
            'details' => $details,
            'labels' => $labels,
            'bensinAwal' => $bensinAwal,
            'bensinAkhir' => $bensinAkhir,
        ]);
    }
}
