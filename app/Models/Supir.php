<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supir extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'no_karyawan',
        'noHP',
        'alamat',
    ];


    public function perjalanan()
    {
        return $this->hasMany(Perjalanan::class);
    }
}
