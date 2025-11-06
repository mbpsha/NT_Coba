<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title',
        'excerpt',
        'content',
        'image',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope untuk filter berita yang published
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope untuk berita terbaru
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
