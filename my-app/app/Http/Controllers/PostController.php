<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

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
}
