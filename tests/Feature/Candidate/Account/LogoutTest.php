<?php

namespace Tests\Feature\Candidate\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'email' => 'john.doe@example.com',
        'password' => 'password',
    ];

    public function testUserCanLogout()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $token = $this->createCandidateToken($user);

        $this->assertCount(1, $user->tokens()->get());

        $response = $this->postJson(route('api.candidate.account.logout'), [], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $this->assertCount(0, $user->tokens()->get());
    }

    public function testUserCannotLogoutWhenNotAuthenticated()
    {
        $response = $this->postJson(route('api.candidate.account.logout'));

        $response->assertUnauthorized();
    }

    public function testUserCannotMakeMoreThanFiveAttemptsInOneMinute()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt('secret'),
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        for ($i = 0; $i <= 5; $i++) {
            $response = $this->postJson(route('api.candidate.auth.login'), $this->payload);
        }

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');

        similar_text(__('auth.throttle'), $response->json('errors.email.0'), $similarity);
        $this->assertTrue($similarity > 75);
    }
}
