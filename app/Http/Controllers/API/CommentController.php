<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CommentController extends BaseController
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request, Post $post)
    {
        try {
            $comment = $post->comments()->create([
                'user_id' => Auth::id(),
                'content' => $request->content,
            ]);

            return $this->sendResponse($comment->load('user'), 'Comment added successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Error creating comment', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, Post $post, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update(['content' => $request->content]);

        return $this->sendResponse($comment->load('user'), 'Comment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        try {
            $comment->delete();

            return $this->sendResponse(null, 'Comment deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error deleting comment', $e->getMessage(), 500);
        }
    }
}
