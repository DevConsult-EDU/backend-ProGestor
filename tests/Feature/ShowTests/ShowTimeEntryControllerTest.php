<?php

namespace Feature\ShowTests;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowTimeEntryControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    /**
     * @test
     * Verifica que se pueda obtener un TimeEntry existente correctamente.
     */
    public function it_can_show_an_existing_time_entry()
    {

        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);
        // 1. Arrange: Crear un TimeEntry en la base de datos.

        $timeEntry = TimeEntry::factory()->create();

        $url = '/api/auth/time-entries/' .  $timeEntry->id;

        // 2. Act: Realizar una petición GET al endpoint del controlador.
        $response = $this->withToken($token)->getJson($url);

        // 3. Assert:
        // - Verificar que la respuesta sea exitosa (200 OK).
        $response->assertStatus(200);
        // - Verificar que la estructura JSON de la respuesta sea la esperada.
        $response->assertJsonStructure([
             'task_id',
             'user_id',
             'date',
             'minutes',
             'description'
        ]);
        // - Verificar que los datos devueltos coincidan con el TimeEntry creado.
         $response->assertJson([
             'task_id' => $timeEntry->task_id,
             'user_id' => $timeEntry->user_id,
             'date' => $timeEntry->date,
             'minutes' => $timeEntry->minutes,
             'description' => $timeEntry->description,
         ]);
    }

    /**
     * @test
     * Verifica que solo se devuelvan los campos esperados.
     * (Este test podría combinarse con 'it_can_show_an_existing_time_entry'
     *  usando assertJsonStructure y assertJsonCount si la respuesta es un array de objetos,
     *  o verificando que no existan campos extra en la respuesta JSON).
     */
    public function it_returns_only_the_expected_fields_for_a_time_entry()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $timeEntry = TimeEntry::factory()->create(['id' => '111']);

        $url = '/api/auth/time-entries/' .  $timeEntry->id;

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonMissing(['id']);

        $response->assertJsonStructure([
            'task_id',
            'user_id',
            'date',
            'minutes',
            'description'
        ]);

        $this->assertCount(5, $response->json(), "La respuesta JSON debería tener exactamente 5 campos.");
    }
}
