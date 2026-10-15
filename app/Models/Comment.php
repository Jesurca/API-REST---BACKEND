<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    // Campos permitidos para asignación masiva (unificando ambos fragmentos)
    protected $fillable = ['content', 'post_id', 'user_id', 'author'];

    /**
     * Publicación a la que pertenece el comentario.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Usuario autor del comentario (si está registrado).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}