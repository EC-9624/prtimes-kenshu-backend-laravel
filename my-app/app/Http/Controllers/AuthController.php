<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Services\AuthService;

class AuthController extends Controller
{

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {

        $this->authService->register($request);

        return redirect()->route('login')->with('success', 'Registered! Please log in.');
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }


    public function login(Request $request): RedirectResponse
    {
        $success = $this->authService->login($request->only('email', 'password'), $request);

        if ($success) {
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
       $this->authService->logout($request);
        return redirect()->route('login');
    }
}


