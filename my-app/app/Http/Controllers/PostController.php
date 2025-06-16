<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\View\View;

/**
 * Show the home page with all posts.
 */
class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::with(['user', 'thumbnail', 'tags'])
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('index', [
            'title' => 'Home Page',
            'data' => $posts,
        ]);
    }

    /**
     * Show posts filtered by a tag slug.
     *
     * @param string $tagSlug The tag slug (e.g., "technology" or "mobile").
     */
    public function showPostsByTag(string $tagSlug): View
    {
        $tag = Tag::where('slug', $tagSlug)->firstOrFail();

        $posts = $tag->posts()
            ->with(['user', 'thumbnail', 'tags'])
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('index', [
            'title' => 'Posts Tagged: ' . $tag->slug, // Assuming 'name' attribute on Tag model
            'data' => $posts,
        ]);
    }
}
