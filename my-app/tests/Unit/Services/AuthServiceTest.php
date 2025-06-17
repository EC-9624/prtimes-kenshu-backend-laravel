<?php

namespace Tests\Unit\Services;

use App\Repositories\UserRepository;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_login_success()
    {

        Auth::shouldReceive('attempt')
            ->once()
            ->with(['email' => 'john@example.com', 'password' => 'password'])
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn((object) ['email' => 'john@example.com']);


        // Create a fake request with a session
        $request = Request::create('/login', 'POST');
        $request->setLaravelSession(Session::driver());

        $userRepoMock = $this->createMock(UserRepository::class);
        $authService = new AuthService($userRepoMock);

        $result = $authService->login([
            'email' => 'john@example.com',
            'password' => 'password',
        ], $request);

        $this->assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function test_login_failure()
    {
        Auth::shouldReceive('attempt')
            ->once()
            ->andReturn(false);

        $request = Request::create('/login', 'POST');
        $request->setLaravelSession(Session::driver());

        $userRepoMock = $this->createMock(UserRepository::class);
        $authService = new AuthService($userRepoMock);

        $result = $authService->login([
            'email' => 'wrong@example.com',
            'password' => 'wrongpass',
        ], $request);

        $this->assertFalse($result);
    }

    /**
     * @throws Exception
     */
    public function test_register_success() {
        $userRepoMock = $this->createMock(UserRepository::class);
        $userRepoMock->expects($this->once())
            ->method('create')
            ->with([
                'user_name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $authService = new AuthService($userRepoMock);

        $result = $authService->register([
            'user_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertTrue($result);
    }
}
