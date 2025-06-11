<?php

namespace Tests\Feature;

use App\Services\DashboardDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class DashboardAISummaryControllerTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Prueba el caso exitoso donde se genera un resumen correctamente.
     * @test
     */
    public function it_successfully_generates_a_summary_using_facade_swap(): void
    {
        // 1. ARRANGE (Preparación)

        // a) Datos falsos (sin cambios)
        $fakeActivities = new Collection([['comment' => 'Se completó el diseño inicial.']]);
        $fakeProjects = new Collection([['name' => 'Proyecto Titán', 'task_count' => 10]]);
        $fakeTasks = new Collection([['title' => 'Revisar wireframes']]);

        // b) Mock del servicio de datos (sin cambios)
        $this->mock(DashboardDataService::class, function (MockInterface $mock) use ($fakeActivities, $fakeProjects, $fakeTasks) {
            $mock->shouldReceive('getRecentActivities')->once()->andReturn($fakeActivities);
            $mock->shouldReceive('getActiveProjects')->once()->andReturn($fakeProjects);
            $mock->shouldReceive('getPendingTasks')->once()->andReturn($fakeTasks);
        });

        // c) Configuración de la API Key y respuesta falsa (sin cambios)
        $fakeApiKey = 'test-api-key';
        $fakeSummary = 'Resumen: El Proyecto Titán avanza bien.';
        config(['services.gemini.api_key' => $fakeApiKey]);

        // =========================================================================
        // d) LA NUEVA LÓGICA DE MOCKING:
        // =========================================================================

        // Primero, creamos un mock del objeto que es devuelto por `generateContent`
        $mockResult = Mockery::mock();
        $mockResult->shouldReceive('text')->once()->andReturn($fakeSummary);

        // Segundo, creamos un mock del objeto que es devuelto por `gemini15Flash`
        $mockModel = Mockery::mock();
        $mockModel->shouldReceive('generateContent')
            ->once()
            // Verificamos el prompt como antes
            ->with(Mockery::on(function ($prompt) {
                return Str::containsAll($prompt, [
                    'Se completó el diseño inicial.',
                    'Proyecto Titán',
                    'Revisar wireframes',
                ]);
            }))
            ->andReturn($mockResult);

        // Tercero, creamos un mock del objeto que es devuelto por `client()`
        $mockClient = Mockery::mock();
        $mockClient->shouldReceive('gemini15Flash')->once()->andReturn($mockModel);

        // Cuarto, creamos un mock del objeto RAÍZ que reemplazará al Facade
        $geminiRootMock = Mockery::mock();
        $geminiRootMock->shouldReceive('client')
            ->once()
            ->with($fakeApiKey)
            ->andReturn($mockClient); // Le decimos que devuelva el mock anterior

        // Finalmente, usamos swap() para reemplazar el objeto real del Facade
        // con nuestro objeto raíz completamente simulado.
        \Gemini::swap($geminiRootMock);

        // =========================================================================

        // 2. ACT (Acción - sin cambios)
        $response = $this->get(route('dashboard.summary'));

        // 3. ASSERT (Verificación - sin cambios)
        $response->assertStatus(200);
        $response->assertJson(['summary' => $fakeSummary]);
    }
}
