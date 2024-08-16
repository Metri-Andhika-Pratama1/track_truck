<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perjalanan extends Model
{
    use HasFactory;

    protected $table = 'perjalanans';

    protected $fillable = [
        'supir_id',
        'truk_id',
        'gudang_id',
        'lat_berangkat',
        'lng_berangkat',
        'lat_tujuan',
        'lng_tujuan',
        'bensin_awal',
        'bensin_akhir'
    ];

    // Relasi ke model Supir
    public function supir()
    {
        return $this->belongsTo(Supir::class);
    }

    // Relasi ke model Truk
    public function truk()
    {
        return $this->belongsTo(Truk::class);
    }

    // Relasi ke model Gudang
    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    // Relasi ke model DetailPerjalanan
    public function details()
    {
        return $this->hasMany(DetailPerjalanan::class, 'perjalanan_id');
    }

    
}
