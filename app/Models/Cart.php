<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_keranjang';

    protected $fillable = [
        'id_user',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function cartDetails()
    {
        return $this->hasMany(CartDetail::class, 'id_keranjang');
    }

    // Accessor untuk total harga
    public function getTotalHargaAttribute()
    {
        return $this->cartDetails->sum(function ($detail) {
            return $detail->jumlah * $detail->harga_satuan;
        });
    }
}
