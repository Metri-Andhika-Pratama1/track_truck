<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPerjalanan extends Model
{
    use HasFactory;

    protected $table = 'detail_perjalanans';

    protected $fillable = [
        'perjalanan_id',
        'lat',
        'lng',
        'minyak',
    ];

    public function perjalanan()
    {
        return $this->belongsTo(Perjalanan::class, 'perjalanan_id');
    }
}
