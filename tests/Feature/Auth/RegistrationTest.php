<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function new_user_can_register_successfully()
    {

        $url = '/api/register';

        $userData = [
            'name' => 'TestUser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson($url, $userData)
            ->assertStatus(201)
            ->assertJsonStructure([]);
    }
}
