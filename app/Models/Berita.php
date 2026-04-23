<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'link',
        'author',
        'kategori',
        'tanggal_publish'
    ];
    protected $casts = [
        'tanggal_publish' => 'datetime',
    ];
}
