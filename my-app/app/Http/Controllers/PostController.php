<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\View\View;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(): View
    {
        $posts = $this->postService->getAllPosts();

        return view('index', [
            'title' => 'Home Page',
            'data' => $posts,
        ]);
    }

    public function showPostsByTag(string $tagSlug): View
    {
        $posts = $this->postService->getPostsByTagSlug($tagSlug);

        return view('index', [
            'title' => 'Posts Tagged: ' . $tagSlug,
            'data' => $posts,
        ]);
    }

    public function showPost(string $slug): View
    {
        return view('index');
    }
}
