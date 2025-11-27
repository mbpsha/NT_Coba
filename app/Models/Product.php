<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga',
        'gambar',
        'stok',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];

    // Relations
    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_produk');
    }

    public function cartDetails()
    {
        return $this->hasMany(CartDetail::class, 'id_produk');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'id_produk');
    }
}
