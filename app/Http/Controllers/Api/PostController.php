<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ContentFilterService;
use App\Models\Post;
use App\Models\Report;
use Exception;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    protected $contentFilterService;

    public function __construct(ContentFilterService $contentFilterService)
    {
        $this->contentFilterService = $contentFilterService;
    }

    public function index()
    {
        try {
            $user = Auth::user();
            $posts = Post::where("user_id",$user->id)->get();
            return response()->json(['posts' => $posts], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch posts.', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

             $validatedData['user_id'] =  Auth::user()->id;


            if ($this->contentFilterService->filter($validatedData['content'])) {
                return response()->json(['message' => 'Content flagged as harmful'], 422);
            }

            $post = new Post();
            $post->title = $validatedData['title'];
            $post->content = $validatedData['content'];
            $post->user_id = $validatedData['user_id'];
            $post->save();

            return response()->json(['post' => $post, 'message' => 'Post created successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create post.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $post = Post::findOrFail($id);
            return response()->json(['post' => $post], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Post not found.', 'error' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            if ($this->contentFilterService->filter($validatedData['content'])) {
                return response()->json(['message' => 'Content flagged as harmful'], 422);
            }

            $post = Post::findOrFail($id);
            $post->title = $validatedData['title'];
            $post->content = $validatedData['content'];
            $post->save();

            return response()->json(['post' => $post, 'message' => 'Post updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update post.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();
            return response()->json(['message' => 'Post deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete post.', 'error' => $e->getMessage()], 500);
        }
    }

    public function report(Request $request)
    {
        try {
            $request->validate([
                'post_id' => 'required|integer',
                'reason' => 'required|string|max:255',
            ]);

            $post = Post::findOrFail($request->post_id);
            $user = Auth::user();

            //user not allowed to report his own post
            if ($user->id === $post->user_id) {
                return response()->json(['message' => 'You cannot report your own post.'], 403);
            }

            $input = [
                "user_id" => $user->id,
                "post_id" => $post->id,
                "reason" => $request->reason,
            ];

            Report::create($input);
            return response()->json(['message' => 'Post reported successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to report post.', 'error' => $e->getMessage()], 500);
        }
    }
}
