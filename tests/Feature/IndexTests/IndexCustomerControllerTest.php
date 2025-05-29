<?php

namespace Feature\IndexTests;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexCustomerControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    /**
     * @test
     */
    public function invoke_returns_ok_status_and_correct_json_structure_when_customers_exist()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers';

        Customer::factory()->count(20)->create();

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonCount(20);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'email',
                'phone',
                'address',
                'created_at',
            ]
        ]);


    }

    /**
     * @test
     */
    public function invoke_returns_ok_status_and_empty_array_when_no_customers_exist()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers';

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonStructure([]);

    }

}
