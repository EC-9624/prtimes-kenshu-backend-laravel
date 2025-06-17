<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthService
{
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
}
