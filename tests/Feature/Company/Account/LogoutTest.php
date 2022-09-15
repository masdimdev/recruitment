<?php

namespace Tests\Feature\Company\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'email' => 'john.doe@company.com',
        'password' => 'password',
    ];

    public function testUserCanLogout()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $token = $this->createCompanyToken($user);

        $this->assertCount(1, $user->tokens()->get());

        $response = $this->postJson(route('api.company.account.logout'), [], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $this->assertCount(0, $user->tokens()->get());
    }

    public function testUserCannotLogoutWhenNotAuthenticated()
    {
        $response = $this->postJson(route('api.company.account.logout'));

        $response->assertUnauthorized();
    }

    public function testUserCannotMakeMoreThanFiveAttemptsInOneMinute()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt('secret'),
            'user_type' => User::TYPE_COMPANY,
        ]);

        for ($i = 0; $i <= 5; $i++) {
            $response = $this->postJson(route('api.company.auth.login'), $this->payload);
        }

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');

        similar_text(__('auth.throttle'), $response->json('errors.email.0'), $similarity);
        $this->assertTrue($similarity > 75);
    }
}
