<?php

namespace Tests\Feature;

use App\Events\UserCVUploadedEvent;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function upload_cv()
    {
        Event::fake();

        // Given we have a test user and a profile
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $path = __DIR__ . '/data/' . 'testresume.pdf';
        $file = new UploadedFile($path, 'testresume.pdf', 'application/pdf', null, true);

        // When we send a POST request to upload the cv
        $this->postJson('/api/account/upload_cv', [
            'cv_file' => $file,
        ]);

        // Then we assert that the UserCVUploadedEvent was dispatched
        Event::assertDispatched(UserCVUploadedEvent::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }
}
