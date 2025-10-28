<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_payment';

    // Only QRIS is allowed for 'metode_pembayaran'
    protected $fillable = [
        'id_order',
        'metode_pembayaran', // Only QRIS
        'jumlah',
        'status',
        'bukti_transfer',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    // Relations
    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }
}
