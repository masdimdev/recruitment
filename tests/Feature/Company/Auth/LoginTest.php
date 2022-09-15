<?php

namespace Tests\Feature\Company\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'email' => 'john.doe@company.com',
        'password' => 'password',
    ];

    public function testUserCanLoginWithCorrectCredentials()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $response = $this->postJson(route('api.company.auth.login'), $this->payload);

        $response->assertSuccessful();
    }

    public function testUserCannotLoginWithCandidateCredentials()
    {
        User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $this->payload['email'] = 'john.doe@example.com';

        User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $response = $this->postJson(route('api.company.auth.login'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    public function testUserCannotLoginWithIncorrectPassword()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $this->payload['password'] = 'invalid-password';

        $response = $this->postJson(route('api.company.auth.login'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    public function testUserCannotLoginWithInvalidEmail()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $this->payload['email'] = 'invalid-email';

        $response = $this->postJson(route('api.company.auth.login'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    public function testUserCannotLoginWithEmailThatDoesNotExist()
    {
        $response = $this->postJson(route('api.company.auth.login'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    public function testUserCanGetHisAccountDetailWithAccessToken()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $response = $this->postJson(route('api.company.auth.login'), $this->payload);

        $response->assertSuccessful();

        $response = $this->getJson(route('api.company.account.index'), [
            'Authorization' => "Bearer {$response->json('data.access_token')}"
        ]);

        $response->assertSuccessful();

        $this->assertEquals($this->payload['email'], $response->json('data.email'));
    }

    public function testUserCannotGetHisAccountDetailWithoutAccessToken()
    {
        $response = $this->getJson(route('api.company.account.index'));

        $response->assertUnauthorized();
    }

    public function testUserCannotGetHisAccountDetailWithInvalidAccessToken()
    {
        $response = $this->getJson(route('api.company.account.index'), [
            'Authorization' => "Bearer this-is-invalid-token}"
        ]);

        $response->assertUnauthorized();
    }
}
