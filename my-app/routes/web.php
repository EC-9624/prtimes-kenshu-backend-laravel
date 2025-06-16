<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\PostController;

Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('categories/{tagSlug}', [PostController::class, 'showPostsByTag'])->name('posts.byTag');
Route::get('posts/{postSlug}', [PostController::class, 'showPost'])->name('post');
