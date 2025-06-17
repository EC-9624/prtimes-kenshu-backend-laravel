<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create([
            'user_name' => $data['user_name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
        ]);
    }
}


