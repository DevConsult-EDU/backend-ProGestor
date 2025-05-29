<?php

namespace Feature\StoreTests;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoreCustomerControllerTest extends TestCase
{
    use RefreshDatabase; // Para resetear la BD con cada test
    use WithFaker; // Para generar datos falsos si es necesario

    /**
     * @test
     */
    public function customer_can_be_created_successfully(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        $customerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id', 'name', 'email', 'phone', 'address'
        ]);

        $response->assertJson([
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address']
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_name_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        $customerData = [
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['name' => 'The name field is required']);

        $this->assertDatabaseMissing('customers', [
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_name_is_too_long(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        $customerData = [
            'name' => str_repeat('a', 256),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['name' => 'The name field must not be greater than 255 characters.']);

        $this->assertDatabaseMissing('customers', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address']
        ]);

    }

    /**
     * @test
     * Prueba que la validación falla si falta el email.
     */
    public function validation_fails_if_email_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        $customerData = [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['email' => 'The email field is required']);

        $this->assertDatabaseMissing('customers', [
            'name' => $customerData['name'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address']
        ]);

    }

    /**
     * @test
     * Prueba que la validación falla si el email es demasiado largo.
     */
    public function validation_fails_if_email_is_too_long(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';
        $longPart = "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
        $longEmail = $longPart.'@example.com';

        $customerData = [
            'name' => $this->faker->name,
            'email' => $longEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['email' => 'The email field must not be greater than 255 characters.']);

        $this->assertDatabaseMissing('customers', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address']
        ]);
    }

    /**
     * @test
     * Prueba que la validación falla si el email no tiene un formato válido.
     */
    public function validation_fails_if_email_is_not_valid_email(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        $customerData = [
            'name' => $this->faker->name,
            'email' => 'this-is-not-valid-email-address',
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['email' => 'The email field must be a valid email address']);

        $this->assertDatabaseMissing('customers', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address']
        ]);
    }

    /**
     * @test
     * Prueba que la validación falla si el email ya existe en la base de datos.
     */
    public function validation_fails_if_email_already_exists(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        Customer::factory()->create(['email' => 'Edu1@ejemplo.com']);

        $customerData = [
            'name' => $this->faker->name,
            'email' => 'Edu1@ejemplo.com',
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['email' => 'The email has already been taken.']);

        $this->assertDatabaseCount('customers', 1);
    }

    /**
     * @test
     * Prueba que la validación falla si falta la contraseña.
     */
    public function validation_fails_if_phone_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        $customerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'address' => $this->faker->address,
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['phone' => 'The phone field is required']);

        $this->assertDatabaseMissing('customers', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'address' => $customerData['address']
        ]);

    }

    /**
     * @test
     * Prueba que la validación falla si falta la contraseña.
     */
    public function validation_fails_if_phone_is_empty(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        $customerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '',
            'address' => $this->faker->address,
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['phone' => 'The phone field is required']);

        $this->assertDatabaseMissing('customers', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address']
        ]);

    }

    /**
     * @test
     * Prueba que la validación falla si falta la contraseña.
     */
    public function validation_fails_if_address_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        $customerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['address' => 'The address field is required']);

        $this->assertDatabaseMissing('customers', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
        ]);
    }

    /**
     * @test
     * Prueba que la validación falla si falta la contraseña.
     */
    public function validation_fails_if_address_is_empty(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        $customerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => '',
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['address' => 'The address field is required']);

        $this->assertDatabaseMissing('customers', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address']
        ]);

    }

    /**
     * @test
     * Prueba que la validación falla si falta la contraseña.
     */
    public function validation_fails_if_address_is_too_long(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/customers/createCustomer';

        $customerData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => str_repeat('a', 256),
        ];

        $response = $this->withToken($token)->postJson($url, $customerData);

        $response->assertStatus(422);

        $response->assertInvalid(['address' => 'The address field must not be greater than 255 characters.']);

        $this->assertDatabaseMissing('customers', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'address' => $customerData['address']
        ]);
    }
}
