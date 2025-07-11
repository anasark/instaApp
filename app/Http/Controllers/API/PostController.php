<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;


class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['user', 'comments', 'likes'])->latest()->get();

        return $this->sendResponse($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        try {
            $imagePath = $this->storeImage($request->file('image'));

            $post = Post::create([
                'user_id' => Auth::id(),
                'caption' => $request->caption,
                'image'   => $imagePath,
            ]);

            return $this->sendResponse($post->load('user'), 'Post created successfully.', 201);
        } catch (\Exception $e) {
            return $this->sendError('Error creating post', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return $this->sendResponse($post->load(['user', 'comments.user', 'likes']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update(['caption' => $request->caption]);

        return $this->sendResponse($post, 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        try {
            if ($post->image) {
                Storage::delete('public/' . $post->image);
            }

            $post->delete();

            return $this->sendResponse([], 'Post deleted successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error deleting post', $e->getMessage(), 500);
        }
    }

    /**
     * @param \Illuminate\Http\UploadedFile $image
     *
     * @return string
     */
    private function storeImage(UploadedFile $image): string
    {
        $filename = time() . '.' . $image->getClientOriginalExtension();

        $img = Image::read($image)->resize(500, 500);

        $path = 'uploads/' . $filename;

        Storage::disk('public')->put(
            $path,
            $img->encodeByExtension($image->getClientOriginalExtension(), quality: 70)
        );

        return $path;
    }
}
