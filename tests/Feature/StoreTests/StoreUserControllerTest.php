<?php

namespace Feature\StoreTests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

// Asegúrate de importar el modelo User si necesitas crearlo para pruebas de unicidad

class StoreUserControllerTest extends TestCase
{
    use RefreshDatabase; // Para resetear la BD con cada test
    use WithFaker; // Para generar datos falsos si es necesario

    /**
     * @test
     * Prueba la creación exitosa de un usuario con datos válidos.
     */
    public function user_can_be_created_successfully(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // TODO: Implementar lógica del test
        $url = '/api/users/createUser';

        // 1. Preparar datos válidos (usando $this->faker si es necesario)
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => "password123",
            'rol' => 'user',
        ];

        // 2. Hacer una petición POST a la ruta del controlador
        $response = $this->withToken($token)->postJson($url, $userData);

        // 3. Assert status 200 o 201
        $response->assertStatus(200);

        // 4. Assert que la respuesta JSON tiene la estructura esperada
        $response->assertJsonStructure([
            'id', 'name', 'email', 'rol'
        ]);

        // 5. Assert que la respuesta JSON contiene los datos correctos (name, email, rol)
        $response->assertJson([
            'name'=> $userData['name'],
            'email'=> $userData['email'],
            'rol'=> $userData['rol']
        ]);

        // 6. Opcional: Verifica que el 'id' devuelto es un UUID válido
        //$responseData = $response->json();
        //$this->assertTrue(Str::isUuid($responseData['id']), 'El ID devuelto no es un UUID válido.');

        // 7. Assert que el usuario existe en la base de datos (assertDatabaseHas)
        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'rol' => $userData['rol'],
        ]);

        // 8. Assert que el ID en la respuesta es un UUID (opcional pero bueno)
        $createdUser = User::where('email', $userData['email'])->first();
        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check('password123', $createdUser->password),
            'La contraseña no se guardó hasheada correctamente.'
        );
    }

    /**
     * @test
     * Prueba que la validación falla si falta el nombre.
     */
    public function validation_fails_if_name_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // TODO: Implementar lógica del test
        $url = '/api/users/createUser';

        // 1. Preparar datos sin el campo 'name'
        $userData = [
            'email' => $this->faker->unique()->safeEmail,
            'password' => "password123",
            'rol' => "user"
        ];

        // 2. Hacer una petición POST
        $this->withToken($token)
            ->postJson($url, $userData)
            ->assertValid(['password','email', 'rol'])
            ->assertInvalid(['name' => 'The name field is required.'])
            ->assertStatus(422);

        // 4. Assert que la respuesta JSON contiene un error de validación para 'name'
        //$response->assertInvalid(['name' => 'The name field is required.'])

        // 5. Assert que el usuario NO existe en la base de datos (assertDatabaseMissing o assertDatabaseCount)
        $this->assertDatabaseMissing('users', [
            'email' => $userData['email'],
            'rol' => $userData['rol']
        ]);
    }

    /**
     * @test
     * Prueba que la validación falla si el nombre es demasiado largo.
     */
    public function validation_fails_if_name_is_too_long(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // TODO: Implementar lógica del test
        $url = '/api/users/createUser';

        // 1. Preparar datos con un 'name' > 255 caracteres
        $userData = [
            'name' => str_repeat('a', 256),
            'email' => $this->faker->unique()->safeEmail,
            'password' => "password123",
            'rol' => 'user',
        ];

        // 2. Hacer una petición POST
        $response = $this->withToken($token)->postJson($url, $userData);

        // 3. Assert status 422
        $response->assertStatus(422);

        // 4. Assert que la respuesta JSON contiene un error de validación para 'name' (max:255)
        $response->assertInvalid(['name' => 'The name field must not be greater than 255 characters.']);

        // 5. Assert que el usuario NO existe en la base de datos
        $this->assertDatabaseMissing('users', [
           'name' => $userData['name'],
           'email' => $userData['email'],
           'rol' => $userData['rol']
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

        // TODO: Implementar lógica del test
        $url = '/api/users/createUser';

        // 1. Preparar datos sin el campo 'email'
        $userData = [
            'name' => $this->faker->name,
            'password' => "password123",
            'rol' => 'user',
        ];

        // 2. Hacer una petición POST
        $response = $this->withToken($token)->postJson($url, $userData);

        // 3. Assert status 422
        $response->assertStatus(422);

        // 4. Assert que la respuesta JSON contiene un error de validación para 'email'
        $response->assertInvalid(['email' => 'The email field is required.']);

        // 5. Assert que el usuario NO existe en la base de datos
        $this->assertDatabaseMissing('users', [
            'name' => $userData['name'],
            'rol' => $userData['rol']
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

        // TODO: Implementar lógica del test
        $url = '/api/users/createUser';
        $longPart = "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
        $longEmail = $longPart.'@example.com';

        // 1. Preparar datos con un 'email' > 255 caracteres (asegúrate de que siga siendo un formato válido de email)
        $userData = [
            'name' => $this->faker->name,
            'email' => $longEmail,
            'password' => "password123",
            'rol' => 'user',
        ];

        // 2. Hacer una petición POST
        $response = $this->withToken($token)->postJson($url, $userData);

        // 3. Assert status 422
        $response->assertStatus(422);

        // 4. Assert que la respuesta JSON contiene un error de validación para 'email' (max:255)
        $response->assertInvalid(['email' => 'The email field must not be greater than 255 characters.']);

        // 5. Assert que el usuario NO existe en la base de datos
        $this->assertDatabaseMissing('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'rol' => $userData['rol']
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

        // TODO: Implementar lógica del test
        $url = '/api/users/createUser';

        // 1. Preparar datos con un 'email' inválido (ej: "texto-plano")
        $userData = [
            'name' => $this->faker->name,
            'email' => 'texto-plano-invalido',
            'password' => "password123",
            'rol' => 'user',
        ];

        // 2. Hacer una petición POST
        $response = $this->withToken($token)->postJson($url, $userData);

        // 3. Assert status 422
        $response->assertStatus(422);

        // 4. Assert que la respuesta JSON contiene un error de validación para 'email' (email format)
        $response->assertInvalid(['email' => 'The email field must be a valid email address.']);

        // 5. Assert que el usuario NO existe en la base de datos
        $this->assertDatabaseMissing('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'rol' => $userData['rol']
        ]);

    }

    /**
     * @test
     * Prueba que la validación falla si el email ya existe en la base de datos.
     */
    public function validation_fails_if_email_already_exists(): void
    {
        $user = User::factory()->create(['email' => 'Edu@ejemplo.com']);

        $token = JWTAuth::fromUser($user);

        // TODO: Implementar lógica del test
        $url = '/api/users/createUser';

        // 2. Preparar datos para un nuevo usuario usando el MISMO email que el usuario existente
        $userData = [
            'name' => $this->faker->name,
            'email' => 'Edu@ejemplo.com',
            'password' => "password123",
            'rol' => 'user',
        ];

        // 3. Hacer una petición POST
        $response = $this->withToken($token)->postJson($url, $userData);

        // 4. Assert status 422
        $response->assertStatus(422);

        // 5. Assert que la respuesta JSON contiene un error de validación para 'email' (unique)
        $response->assertInvalid(['email' => 'The email has already been taken.']);

        // 6. Assert que NO se creó un segundo usuario con ese email (assertDatabaseCount('users', 1))
        $this->assertDatabaseCount('users', 1);

    }

    /**
     * @test
     * Prueba que la validación falla si falta la contraseña.
     */
    public function validation_fails_if_password_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // TODO: Implementar lógica del test
        $url = '/api/users/createUser';

        // 1. Preparar datos sin el campo 'password'
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'rol' => 'user',
        ];

        // 2. Hacer una petición POST
        $response = $this->withToken($token)->postJson($url, $userData);

        // 3. Assert status 422
        $response->assertStatus(422);

        // 4. Assert que la respuesta JSON contiene un error de validación para 'password'
        $response->assertInvalid(['password' => 'The password field is required.']);

        // 5. Assert que el usuario NO existe en la base de datos
        $this->assertDatabaseMissing('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'rol' => $userData['rol'],
        ]);

    }

    /**
     * @test
     * Prueba que la validación falla si la contraseña está vacía (falla min:1).
     */
    public function validation_fails_if_password_is_empty(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // TODO: Implementar lógica del test
        $url = '/api/users/createUser';

        // 1. Preparar datos con 'password' => ''
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '',
            'rol' => 'user',
        ];

        // 2. Hacer una petición POST
        $response = $this->withToken($token)->postJson($url, $userData);

        // 3. Assert status 422
        $response->assertStatus(422);

        // 4. Assert que la respuesta JSON contiene un error de validación para 'password' (min:1)
        $response->assertInvalid(['password' => 'The password field is required.']);

        // 5. Assert que el usuario NO existe en la base de datos
        $this->assertDatabaseMissing('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'rol' => $userData['rol'],
        ]);

    }

    /**
     * @test
     * Prueba que un usuario puede ser creado incluso si el campo 'rol' no se envía.
     * (Asume que la columna 'rol' en la BD es nullable o tiene un valor por defecto)
     */
    public function validation_fails_if_rol_is_empty(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // TODO: Implementar lógica del test
        $url = '/api/users/createUser';

        // 1. Preparar datos válidos EXCEPTO por el campo 'rol' (no incluirlo)
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => "password123",
            'rol' => ''
        ];

        // 2. Hacer una petición POST
        $response = $this->withToken($token)->postJson($url, $userData);

        // 3. Assert status 200 o 201
        $response->assertStatus(422);

        // 4. Assert que la respuesta JSON contiene 'rol' => null (o el valor por defecto si lo hubiera)
        $response->assertInvalid(['rol' => 'The rol field is required.']);

        // 5. Assert que el usuario existe en la base de datos con 'rol' => null (o el valor por defecto)
        $this->assertDatabaseMissing('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'rol' => $userData['rol'],
        ]);

    }

    // Nota: Necesitarás definir la ruta que apunta a tu StoreUserController en
    // tus archivos de rutas (ej: routes/api.php o routes/web.php) para poder
    // hacer las peticiones POST en los tests. Por ejemplo:
    // Route::post('/api/users', App\Http\Controllers\private\StoreUserController::class);
    // Y en los tests harías: $this->postJson('/api/users', $userData);

}
