<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
    }

    public function test_it_creates_a_user_with_hashed_password()
    {
        $data = [
            'user_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $user = $this->userRepository->create($data);

        // Assert it's stored in the DB
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'user_name' => 'Test User',
        ]);

        // Assert the returned user is correct
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->user_name);
        $this->assertEquals('test@example.com', $user->email);

        // Assert password is hashed
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_it_throws_exception_on_duplicate_email()
    {
        $data = [
            'user_name' => 'First User',
            'email' => 'test@example.com',
            'password' => 'secret123',
        ];

        // Create first user
        $this->userRepository->create($data);

        $this->expectException(QueryException::class);

        // Try creating another user with same email (should fail)
        $this->userRepository->create([
            'user_name' => 'Second User',
            'email' => 'test@example.com',
            'password' => 'anothersecret',
        ]);
    }

}
