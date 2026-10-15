<h2>Nuevo comentario en tu publicación</h2>
<p><strong>{{ $comment->user->name }}</strong> comentó en «{{ $comment->post->title }}»:</p>
<blockquote>{{ $comment->content }}</blockquote>