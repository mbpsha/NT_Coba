<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_order';

    protected $fillable = [
        'id_user',
        'status',
        'total_harga',
        'id_alamat',
        'shipping_cost',
        'admin_fee',
        'shipping_weight',
        'shipping_destination_city_id',
        'shipping_courier',
        'shipping_service',
        'shipping_etd',
        'shipping_is_estimated',
    ];

    protected $casts = [
        'total_harga' => 'integer',
        'shipping_cost' => 'integer',
        'admin_fee' => 'integer',
        'shipping_weight' => 'integer',
        'shipping_is_estimated' => 'integer',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'id_order', 'id_order');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'id_order', 'id_order');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'id_alamat', 'id_alamat');
    }
}
