<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index() :View
    {
        $errors = Session::get('errors');
        Session::forget('errors');

        $postQuery = Post::with(['user', 'thumbnail', 'tags'])
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc');

        $posts = $postQuery->get();

        return view('index', [
            'title' => 'Home Page',
            'data' => $posts,
            'errors' => $errors
        ])->withStatus(200);
    }
}
