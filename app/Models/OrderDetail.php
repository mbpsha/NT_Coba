<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_detail_pemesanan';

    protected $fillable = [
        'id_pemesanan',
        'id_produk',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relations
    public function order()
    {
        return $this->belongsTo(Order::class, 'id_pemesanan', 'id_pemesanan');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }
}
