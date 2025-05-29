<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_login_a_user_with_valid_credentials()
    {
        $url = '/api/login';
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('123456'),
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => '123456',
        ];

        // Act
        $response = $this->postJson($url, $credentials);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['token']);

        // Optionally, you can assert that a token was actually generated
        $this->assertNotEmpty($response->json('token'));
    }

    /** @test */
    public function it_returns_unauthorized_for_invalid_credentials()
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
        ]);
        $credentials = [
            'email' => $user->email,
            'password' => 'wrong-password',
        ];

        // Act
        $response = $this->postJson('/api/login', $credentials);

        // Assert
        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid credentials']);
    }

    /** @test */
    public function it_returns_internal_server_error_if_token_cannot_be_created()
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
        ]);
        $credentials = [
            'email' => $user->email,
            'password' => 'secret',
        ];

        // Mock JWTAuth::attempt to throw an exception
        JWTAuth::shouldReceive('attempt')
            ->once()
            ->with($credentials)
            ->andThrow(new \Tymon\JWTAuth\Exceptions\JWTException);

        // Act
        $response = $this->postJson('/api/login', $credentials);

        // Assert
        $response->assertStatus(500)
            ->assertJson(['error' => 'Could not create token']);
    }
}
