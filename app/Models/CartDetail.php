<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_detail_keranjang';

    protected $fillable = [
        'id_keranjang',
        'id_produk',
        'jumlah',
        'harga_satuan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
    ];

    // Relations
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'id_keranjang', 'id_keranjang');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }

    // Accessor untuk subtotal
    public function getSubtotalAttribute()
    {
        return $this->jumlah * $this->harga_satuan;
    }
}
