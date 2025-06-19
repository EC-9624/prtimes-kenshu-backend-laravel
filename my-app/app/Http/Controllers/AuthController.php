<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $validated = $request->validate([
            'user_name'             => 'required|string|max:255',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);


        $this->authService->register($validated);

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
            'email' => 'Invalid Email or Password.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
       $this->authService->logout($request);
        return redirect()->route('login');
    }
}


