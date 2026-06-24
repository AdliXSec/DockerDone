<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    // WAJIB: Sesuaikan dengan nama tabel aslimu di phpMyAdmin
    protected $table = 'obat';

    // Mengizinkan semua kolom untuk diisi (Mass Assignment)
    protected $guarded = [];
}
