<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_review';

    protected $fillable = [
        'id_produk',
        'id_user',
        'rating',
        'komentar',
        'dokumentasi',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_produk', 'id_produk');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
