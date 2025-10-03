<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
        'foto',
        'stok',
    ];

    // Relasi dengan tabel cart_details
    public function cartDetails()
    {
        return $this->hasMany(CartDetail::class, 'id_produk');
    }

    // Relasi dengan tabel order_details
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'id_produk');
    }

    // Relasi dengan tabel reviews
    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_produk');
    }

    // Scope untuk produk yang tersedia (stok > 0)
    public function scopeAvailable($query)
    {
        return $query->where('stok', '>', 0);
    }

    // Accessor untuk format harga
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    // Method untuk mengurangi stok
    public function decreaseStock($quantity)
    {
        if ($this->stok >= $quantity) {
            $this->stok -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    // Method untuk menambah stok
    public function increaseStock($quantity)
    {
        $this->stok += $quantity;
        $this->save();
    }
}
