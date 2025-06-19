<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Database\QueryException;
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

    /**
     * @param array $credentials
     * @param Request $request
     * @return bool
     */
    public function login(array $credentials, Request $request): bool
    {
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            Log::info('✅ Logged in: ' . Auth::user()->email);
            return true;
        }

        Log::warning('❌ Login failed');
        return false;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void{
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function register(array $data): bool {
        try {
            $this->userRepository->create($data);
            return true;
        } catch (QueryException $e) {
            Log::error('❌ Failed to register user: ' . $e->getMessage());
            return false;
        }
    }

}
