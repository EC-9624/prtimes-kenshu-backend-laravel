<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\PostController;

Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('categories/{tagSlug}', [PostController::class, 'showPostsByTag'])->name('posts.byTag');
Route::get('posts/{postSlug}', [PostController::class, 'showPost'])->name('post');


Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/debug-auth', function () {
    return [
        'auth_check' => Auth::check(),
        'user' => Auth::user(),
        'session_id' => session()->getId(),
        'cookie' => request()->cookie('laravel_session'),
    ];
});


