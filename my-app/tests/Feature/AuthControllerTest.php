<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_register_form_returns_view()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_register_successfully_creates_user_and_redirects()
    {
        $response = $this->post(route('register.post'), [
            'user_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'user_name' => 'Jane Doe',
        ]);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_register_with_invalid_data_redirects_back_with_errors()
    {
        $response = $this->from(route('register'))->post(route('register.post'), [
            'user_name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'no-match',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors([
            'user_name',
            'email',
            'password',
        ]);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_register_fails_on_duplicate_email()
    {
        // Create user beforehand
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $response = $this->from(route('register'))->post(route('register.post'), [
            'user_name' => 'John Doe',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors(['email']);
        $this->assertEquals(1, User::where('email', 'duplicate@example.com')->count());
    }
}
