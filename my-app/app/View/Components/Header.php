<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Header extends Component
{
    public ?string $userName;
    public ?string $email;

    public function __construct()
    {
        $user = Auth::user();
        $this->userName = $user?->userName;
        $this->email = $user?->email;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.header');
    }
}
