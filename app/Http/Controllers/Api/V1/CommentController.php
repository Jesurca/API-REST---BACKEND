<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\NewCommentMail;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * @group Comentarios
 */
class CommentController extends Controller
{
    /**
     * Comentar una publicación
     *
     * Si quien comenta no es el autor, el autor recibe un correo "Nuevo comentario".
     *
     * @authenticated
     * @urlParam post integer required ID de la publicación. Example: 1
     */
    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment = $post->comments()->create([
            'content' => $data['content'],
            'user_id' => $request->user()->id,
        ]);

        if ($post->user && $post->user_id !== $request->user()->id) {
            Mail::to($post->user)->send(new NewCommentMail($comment));
        }

        return response()->json($comment->load('user:id,name'), 201);
    }
}