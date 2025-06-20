<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\PostController;

Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('categories/{tagSlug}', [PostController::class, 'showPostsByTag'])->name('posts.byTag');
Route::get('posts/{postSlug}', [PostController::class, 'showPost'])->name('post');

Route::middleware('auth')->group(function () {
    Route::get('create-post', [PostController::class, 'showCreatePost'])->name('createPost');
    Route::post('create-post', [PostController::class, 'createPost'])->name('createPost.post');

    Route::get('edit-post/{post}', [PostController::class, 'showEditPost'])->name('editPost');
    Route::patch('edit-post/{post}', [PostController::class, 'editPost'])->name('editPost.post');
});

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




