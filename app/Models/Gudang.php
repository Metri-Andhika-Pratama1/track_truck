<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_gudang',
        'lat',
        'lng',
        
    ];

    public function perjalanan()
    {
        return $this->hasMany(Perjalanan::class);
    }
   
}
