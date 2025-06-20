<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        $posts = $this->postService->getAllPosts();
        return view('index', [
            'title' => 'Home Page',
            'data' => $posts,
        ]);
    }

    /**
     * @param string $tagSlug
     * @return View
     */
    public function showPostsByTag(string $tagSlug): View
    {
        $posts = $this->postService->getPostsByTagSlug($tagSlug);

        return view('index', [
            'title' => 'Posts Tagged: ' . $tagSlug,
            'data' => $posts,
        ]);
    }

    /**
     * @param string $postSlug
     * @return View
     */
    public function showPost(string $postSlug): View
    {
        $post = $this->postService->getPostBySlug($postSlug);

        return view('post',['title' => 'Post Detail Page', 'data' => $post]);
    }

    public function showCreatePost(): View{
        return view('createPost');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function createPost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug',
            'text' => 'required|string',
            'thumbnail_image' => 'nullable|image|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'additional_images' => 'nullable|array',
            'additional_images.*' => 'image|max:5120',
            'tag_slugs' => 'nullable|array',
            'tag_slugs.*' => 'string|exists:tags,slug',
        ]);

        try {
            $this->postService->createPost($validated, Auth::id(), Str::uuid()->toString());

            return redirect()->route('home')->with('success', 'Post successfully created.');
        } catch (Throwable $e) {
            Log::error('Post creation failed: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Create Post failed.');
        }
    }

    public function showEditPost(string $postSlug): View{
        return view('editPost', ['post' => $this->postService->getPostBySlug($postSlug)]);
    }

    /**
     * @param Request $request
     * @param string $postSlug
     * @return RedirectResponse
     * @throws Throwable
     */
    public function editPost(Request $request, string $postSlug): RedirectResponse
    {
        $post = $this->postService->getPostBySlug($postSlug);

        // Check if the current user is the author of the post
        if (!Auth::check() || Auth::id() !== $post->user_id) {
            return redirect()
                ->route('editPost', $postSlug)
                ->withErrors(['You are not authorized to edit this post.']);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($post->post_id, 'post_id'),
            ],
            'text' => 'required|string',
            'tag_slugs' => 'nullable|array',
            'tag_slugs.*' => 'string|exists:tags,slug',
        ]);

        $this->postService->updatePost($postSlug, [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'text' => $validated['text'],
            'tags' => $validated['tag_slugs'] ?? [],
        ]);

        return redirect()
            ->route('post', $validated['slug'])
            ->with('success', 'Post updated successfully!');
    }

    /**
     * @param string $postSlug
     * @return RedirectResponse
     * @throws Throwable
     */
    public function deletePost(string $postSlug): RedirectResponse
    {
        // Resolve once
        $post = $this->postService->getPostBySlug($postSlug);

        // Authorization
        if (!Auth::check() || Auth::id() !== $post->user_id) {
            return redirect()
                ->route('post', $postSlug)
                ->withErrors(['You are not authorized to delete this post.']);
        }

        // Pass model instead of slug
        $this->postService->deletePost($post);

        return redirect()->route('home')->with('success', 'Post deleted successfully.');
    }

}
