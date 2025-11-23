<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'title',
        'slug',          // tambahkan jika memang ada
        'excerpt',
        'content',
        'image',
        'is_published',
        'published_at',  // tambahkan jika kolom dipakai
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // Scope filter published
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Ganti nama dari scopeLatest agar tidak bentrok dengan bawaan latest()
    public function scopeNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}