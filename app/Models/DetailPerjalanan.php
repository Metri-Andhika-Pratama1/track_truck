<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPerjalanan extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi massal
    protected $fillable = [
        'perjalanan_id',
        'lat',
        'lng',
        'minyak'
    ];

    // Mengonversi tipe data atribut
    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'minyak' => 'float', // Jika minyak adalah angka
    ];

    // Relasi ke model Perjalanan
    public function perjalanan()
    {
        return $this->belongsTo(Perjalanan::class);
    }
    
 
}
