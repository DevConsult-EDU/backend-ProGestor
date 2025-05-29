<?php

namespace Feature\UpdateTests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateUserControllerTest extends TestCase
{
    use RefreshDatabase; // Usa este trait para limpiar la base de datos después de cada test
    use WithFaker; // Para generar datos falsos si es necesario

    // --- Variables de configuración comunes ---
    protected $adminUser;
    protected $targetUser;
    protected $updateUrl;

    /**
     * Configuración que se ejecuta antes de cada test en esta clase.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'rol' => 'admin', 'password' => Hash::make('password'),
        ]);


        $this->targetUser = User::factory()->create([
            'password' => Hash::make('oldPassword123'),
        ]);

        $this->updateUrl = '/api/users/updateUser/';
    }

    /**
     * @test
     * Prueba la actualización exitosa de los datos del usuario sin cambiar la contraseña.
     */
    public function test_user_can_be_updated_successfully_without_password(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $newData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'rol' => 'user',
        ];

        $url = $this->updateUrl . $this->targetUser->id;
        $originalPasswordHash = $this->targetUser->password;

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $response->assertJsonMissingPath('password');

        $this->assertDatabaseHas('users', [
            'id' => $this->targetUser->id,
            'name' => $newData['name'],
            'email' => $newData['email'],
            'rol' => $newData['rol'],
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->targetUser->id,
            'password' => $originalPasswordHash,
        ]);
    }

    /**
     * @test
     * Prueba la actualización exitosa de los datos del usuario incluyendo la contraseña.
     */
    public function test_user_can_be_updated_successfully_with_password(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $newPassword = 'newSecurePassword123';

        $newData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $newPassword,
            'rol' => 'user',
        ];

        $url = $this->updateUrl . $this->targetUser->id;

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $this->targetUser->id,
            'name' => $newData['name'],
            'email' => $newData['email'],
            'rol' => $newData['rol'],
        ]);

        $response->assertJsonMissingPath('password');

        $this->assertDatabaseHas('users', [
            'id' => $this->targetUser->id,
            'name' => $newData['name'],
            'email' => $newData['email'],
            'rol' => $newData['rol'],
        ]);

        $this->targetUser->refresh();

        $this->assertTrue(Hash::check($newPassword, $this->targetUser->password));
    }

    /**
     * @test
     * Prueba la validación cuando falta el nombre.
     */
    public function test_validation_fails_if_name_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $newData = [
            'email' => $this->faker->unique()->safeEmail,
            'rol' => 'user',
        ];

        $url = $this->updateUrl . $this->targetUser->id;

        $originalName = $this->targetUser->name;
        $originalEmail = $this->targetUser->email;
        $originalRol = $this->targetUser->rol;

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['name' => 'The name field is required.']);

        $this->assertDatabaseHas('users', [
            'id' => $this->targetUser->id,
            'email' => $originalEmail,
            'rol' => $originalRol,
        ]);


        // Pasos a implementar:
        // 1. ARRANGE: Prepara los datos sin el campo 'name'.
        // 2. ACT: Realiza la petición PUT/PATCH como usuario autenticado (adminUser).
        // 3. ASSERT:
        //    - Verifica que la respuesta tenga un código 400 (Bad Request).
        //    - Verifica que la respuesta JSON contenga un error de validación específico para el campo 'name'. (assertJsonValidationErrors('name'))
        //    - Verifica que los datos del usuario en la base de datos NO hayan cambiado.
    }

    /**
     * @test
     * Prueba la validación cuando falta el email.
     */
    public function test_validation_fails_if_email_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $newData = [
            'name' => $this->faker->name,
            'rol' => 'user',
        ];

        $url = $this->updateUrl . $this->targetUser->id;

        $originalName = $this->targetUser->name;
        $originalEmail = $this->targetUser->email;
        $originalRol = $this->targetUser->rol;

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['email' => 'The email field is required.']);

        $this->assertDatabaseHas('users', [
            'id' => $this->targetUser->id,
            'name' => $originalName,
            'rol' => $originalRol,
        ]);


        // Pasos a implementar:
        // 1. ARRANGE: Prepara los datos sin el campo 'email'.
        // 2. ACT: Realiza la petición PUT/PATCH como usuario autenticado (adminUser).
        // 3. ASSERT:
        //    - Verifica que la respuesta tenga un código 400 (Bad Request).
        //    - Verifica que la respuesta JSON contenga un error de validación específico para el campo 'email'. (assertJsonValidationErrors('email'))
        //    - Verifica que los datos del usuario en la base de datos NO hayan cambiado.
    }

    /**
     * @test
     * Prueba la validación cuando el email ya está en uso por OTRO usuario.
     */
    public function test_validation_fails_if_email_is_already_taken_by_another_user(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = $this->updateUrl . $this->targetUser->id;
        $originalName = $this->targetUser->name;
        $originalEmail = $this->targetUser->email;
        $originalRol = $this->targetUser->rol;

        $initialUserCount = User::count();

        User::factory()->create(['email' => 'Edu@ejemplo.com']);

        $newData = [
            'name' => $this->faker->name,
            'email' => 'Edu@ejemplo.com',
            'rol' => 'user',
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['email' => 'The email has already been taken.']);

        $this->assertDatabaseCount('users', $initialUserCount + 1);

        $this->assertDatabaseHas('users', [
              'id' => $this->targetUser->id,
              'email' => $originalEmail,
        ]);



        // Pasos a implementar:
        // 1. ARRANGE:
        //    - Crea OTRO usuario (`anotherUser`) con un email específico.
        //    - Prepara los datos para actualizar `targetUser`, usando el email de `anotherUser`.
        // 2. ACT: Realiza la petición PUT/PATCH como usuario autenticado (adminUser) para actualizar `targetUser`.
        // 3. ASSERT:
        //    - Verifica que la respuesta tenga un código 400 (Bad Request).
        //    - Verifica que la respuesta JSON contenga un error de validación específico para el campo 'email' (indicando que ya existe). (assertJsonValidationErrors('email'))
        //    - Verifica que los datos del `targetUser` en la base de datos NO hayan cambiado.
    }

    /**
     * @test
     * Prueba que la validación del email único IGNORE el email del propio usuario que se está actualizando.
     */
    public function test_validation_succeeds_if_email_is_unchanged_or_belongs_to_the_same_user(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = $this->updateUrl . $this->targetUser->id;
        $originalName = $this->targetUser->name;
        $originalEmail = $this->targetUser->email;
        $originalRol = $this->targetUser->rol;

        $newData = [
            'name' => $this->faker->name,
            'email' => $originalEmail,
            'rol' => 'user',
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'rol',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->targetUser->id,
            'name' => $newData['name'],
            'email' => $newData['email'],
            'rol' => $newData['rol'],
        ]);


        // Pasos a implementar:
        // 1. ARRANGE: Prepara los datos nuevos, pero manteniendo el email original de `targetUser`.
        // 2. ACT: Realiza la petición PUT/PATCH como usuario autenticado (adminUser) a la URL correcta ($this->updateUrl . $this->targetUser->id) con los datos.
        // 3. ASSERT:
        //    - Verifica que la respuesta tenga un código 200 (OK).
        //    - Verifica que la respuesta JSON NO contenga errores de validación para 'email'. (assertJsonMissingValidationErrors('email'))
        //    - Verifica que la base de datos se haya actualizado correctamente.
    }

    /**
     * @test
     * Prueba la validación cuando se envía una contraseña pero es demasiado corta.
     */
    public function test_validation_fails_if_password_is_too_short(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = $this->updateUrl . $this->targetUser->id;
        $originalName = $this->targetUser->name;
        $originalEmail = $this->targetUser->email;
        $originalRol = $this->targetUser->rol;

        $newData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '123',
            'rol' => 'user',
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['password' => 'The password field must be at least 6 characters.']);

        $this->assertDatabaseHas('users', [
            'id' => $this->targetUser->id,
            'name' => $originalName,
            'email' => $originalEmail,
            'rol' => $originalRol,
        ]);

        // Pasos a implementar:
        // 1. ARRANGE: Prepara los datos incluyendo 'password' y 'password_confirmation' con un valor corto (ej: '123').
        // 2. ACT: Realiza la petición PUT/PATCH como usuario autenticado (adminUser).
        // 3. ASSERT:
        //    - Verifica que la respuesta tenga un código 400 (Bad Request).
        //    - Verifica que la respuesta JSON contenga un error de validación específico para el campo 'password'. (assertJsonValidationErrors('password'))
        //    - Verifica que los datos del usuario en la base de datos NO hayan cambiado (incluida la contraseña).
    }

    /**
     * @test
     * Prueba que un usuario no autenticado no puede actualizar datos.
     */
    public function test_unauthenticated_user_cannot_update_user(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = $this->updateUrl . $this->targetUser->id;

        $newData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'rol' => 'user',
        ];

        $response = $this->withoutToken($token)->putJson($url, $newData);

        $response->assertStatus(401);


        $this->assertDatabaseHas('users', [
            'id' => $this->targetUser->id,
            'name' => $this->targetUser->name,
            'email' => $this->targetUser->email,
            'rol' => $this->targetUser->rol,
        ]);


        // Pasos a implementar:
        // 1. ARRANGE: Prepara datos válidos para la actualización.
        // 2. ACT: Realiza la petición PUT/PATCH SIN autenticar (`$this->putJson(...)` o `$this->patchJson(...)`) a la URL de actualización.
        // 3. ASSERT:
        //    - Verifica que la respuesta tenga un código 401 (Unauthorized) o 403 (Forbidden), o una redirección al login, dependiendo de tu middleware de autenticación.
    }

}
