<?php

namespace Feature\DeleteTests;

use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTimeEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_is_successfully_deleted(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/auth/time-entries/';

        $timeEntryToDelete = TimeEntry::factory()->create();


        $response = $this->withToken($token)->deleteJson($url . $timeEntryToDelete->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('time_entries', [
            'id' => $timeEntryToDelete->id,
        ]);

    }
}
