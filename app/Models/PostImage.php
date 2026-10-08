<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    protected $fillable = ['post_id', 'image_path'];
    protected $appends  = ['image_url'];   // agrega la URL completa al JSON

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}
