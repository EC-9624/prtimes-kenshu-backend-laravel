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

    public function createPost(): View{
        return view('createPost');
    }
}
