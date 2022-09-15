<?php

namespace Tests\Feature\Candidate\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'email' => 'john.doe@example.com',
        'password' => 'password',
    ];

    public function testUserCanGetOwnAccount()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->getJson(route('api.candidate.account.index'), [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $this->assertEquals($this->payload['email'], $response->json('data.email'));
    }
}
