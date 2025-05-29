<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DownloadAttachmentControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Task $task;
    protected Attachment $attachment1;
    protected Attachment $attachment2;
    protected string $testFilePath;

    /**
     * Configuración inicial para las pruebas
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->user = User::factory()->create();

        $this->task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $file = UploadedFile::fake()->create('test_document.pdf', 500);

        $path = $file->store('attachments');
        $this->testFilePath = $path;

        $this->attachment1 = Attachment::create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'file_name' => 'test_document.pdf',
            'system_name' => 'test_document_'.time().'.pdf',
            'type_MIME' => 'application/pdf',
            'byte_size' => $file->getSize(),
            'store_path' => $path
        ]);

        $this->attachment2 = Attachment::create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'file_name' => 'image.png',
            'system_name' => 'image'.time().'.png',
            'type_MIME' => 'image/png',
            'byte_size' => $file->getSize(),
            'store_path' => $path
        ]);
    }

    /**
     *
     * @return void
     */
    public function test_can_download_attachment()
    {

        $token = JWTAuth::fromUser($this->user);

        $url = "/api/attachments/{$this->attachment1->id}/downloadAttachment";

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertHeader('Content-Type', 'application/pdf');

        $response->assertHeader('Content-Disposition', 'attachment; filename="test_document.pdf"');
    }

    /**
     *
     * @return void
     */
    public function test_can_download_attachment_image()
    {

        $token = JWTAuth::fromUser($this->user);

        $url = "/api/attachments/{$this->attachment2->id}/downloadAttachment";

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertHeader('Content-Type', 'image/png');

        $response->assertHeader('Content-Disposition', 'attachment; filename="image.png"');
    }

    /**
     *
     * @return void
     */
    public function test_returns_404_when_attachment_not_found()
    {
        $token = JWTAuth::fromUser($this->user);

        $nonExistentId = 'non-existent-uuid';

        $url = "/api/attachments/{$nonExistentId}/downloadAttachment";

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(404);

        $response->assertJson(['message' => 'attachment not found']);
    }

    /**
     *
     * @return void
     */
    public function test_returns_404_when_file_not_in_storage()
    {
        $token = JWTAuth::fromUser($this->user);

        $url = "/api/attachments/{$this->attachment->id}/downloadAttachment";

        Storage::delete($this->attachment->store_path);

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(404);

        $response->assertJson(['message' => 'The solicited attachment was not found']);
    }

}
