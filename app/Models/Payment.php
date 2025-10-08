<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'id_pemesanan',
        'metode_pembayaran',
        'jumlah',
        'status_pembayaran',
        'bukti_pembayaran',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    // Relations
    public function order()
    {
        return $this->belongsTo(Order::class, 'id_pemesanan', 'id_pemesanan');
    }
}
