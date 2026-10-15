<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

/**
 * @group Publicaciones
 */
class PostController extends Controller
{
    /**
     * Listar publicaciones
     *
     * Devuelve 10 por página con autor, comentarios y total de likes.
     * Permite buscar por título o contenido.
     *
     * @queryParam search string Texto a buscar en título o contenido. Example: viaje
     * @queryParam page integer Número de página. Example: 1
     */
    public function index(Request $request)
    {
        return Post::with(['user:id,name', 'comments.user:id,name'])
            ->withCount('likedBy as likes_count')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Ver una publicación
     *
     * @urlParam post integer required ID de la publicación. Example: 1
     */
    public function show(Post $post)
    {
        return $post->load(['user:id,name', 'comments.user:id,name'])
            ->loadCount('likedBy as likes_count');
    }

    /**
     * Crear publicación
     *
     * @authenticated
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $post = Post::create($data + ['user_id' => $request->user()->id]);

        return response()->json($post->load('user:id,name'), 201);
    }

    /**
     * Eliminar publicación
     *
     * Solo el autor puede eliminarla.
     *
     * @authenticated
     * @urlParam post integer required ID de la publicación. Example: 1
     */
    public function destroy(Request $request, Post $post)
    {
        abort_if(
            $post->user_id !== $request->user()->id,
            403,
            'No puedes eliminar publicaciones de otros usuarios.'
        );

        $post->delete();

        return response()->noContent();
    }

    /**
     * Dar o quitar like
     *
     * Si ya habías dado like lo quita; si no, lo agrega.
     *
     * @authenticated
     * @urlParam post integer required ID de la publicación. Example: 1
     * @response {"liked":true,"likes_count":3}
     */
    public function like(Request $request, Post $post)
    {
        $result = $post->likedBy()->toggle($request->user()->id);

        return response()->json([
            'liked'       => count($result['attached']) > 0,
            'likes_count' => $post->likedBy()->count(),
        ]);
    }
}