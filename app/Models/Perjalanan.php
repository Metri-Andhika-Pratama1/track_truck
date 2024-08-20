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
        'bensin_akhir',
    ];

    public function supir()
    {
        return $this->belongsTo(Supir::class, 'supir_id');
    }

    public function truk()
    {
        return $this->belongsTo(Truk::class, 'truk_id');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

    public function detail_perjalanans()
    {
        return $this->hasMany(DetailPerjalanan::class, 'perjalanan_id');
    }
}
