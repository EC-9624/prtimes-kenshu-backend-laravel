<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function login(array $credentials, Request $request): bool
    {
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            Log::info('✅ Logged in: ' . Auth::user()['email']);
            return true;
        }

        Log::warning('❌ Login failed');
        return false;
    }

    public function logout(Request $request): void{
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function register(Request $request): bool
    {
        $validated = $request->validate([
            'user_name'             => 'required|string|max:255',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);

        $this->userRepository->create($validated);

        return true;
    }
}
