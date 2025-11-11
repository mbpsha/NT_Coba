<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_payment';

    protected $fillable = [
        'id_order',
        'metode_pembayaran',
        'jumlah',
        'status',
        'bukti_transfer',
        'trx_id', // ditambahkan
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order')
            ->with(['user','orderDetails.product']);
    }
}
