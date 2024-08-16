<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truk extends Model
{
    use HasFactory;


    protected $fillable = [
        'plat_no',
        'manufaktur',
        'seri',
        'tahun_pembuatan'
    ];
    
    public function perjalanan()
    {
        return $this->hasMany(Perjalanan::class);
    }
}
