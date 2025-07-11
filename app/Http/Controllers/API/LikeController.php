<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post)
    {
        try {
            $like = $post->likes()->firstOrCreate([
                'user_id' => Auth::id()
            ]);

            return $this->sendResponse($like->load('user'), 'Post liked successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Error liking post', $e->getMessage(), 500);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            $deleted = $post->likes()
                ->where('user_id', Auth::id())
                ->delete();

            if ($deleted) {
                return $this->sendResponse(null, 'Like removed successfully');
            }

            return $this->sendError('Like not found', null, 404);
        } catch (\Exception $e) {
            return $this->sendError('Error removing like', $e->getMessage(), 500);
        }
    }
}
