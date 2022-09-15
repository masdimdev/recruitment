<?php

namespace Tests\Feature\Candidate\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',

        'phone_number' => '6281234567890',
        'address' => 'Trenggalek, East Java',
        'date_of_birth' => '1991-01-01',
        'sex' => 1,
    ];

    private array $requiredFields = [
        'first_name',
        'last_name',
        'email',
        'password',
        'password_confirmation',

        'phone_number',
        'address',
        'date_of_birth',
        'sex',
    ];

    public function testUserCanRegisterWithCorrectData()
    {
        $response = $this->postJson(route('api.candidate.auth.register'), $this->payload);

        $response->assertSuccessful();

        $user = User::with('candidateProfile')->first();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($this->payload['email'], $user->email);
        $this->assertEquals(User::TYPE_CANDIDATE, $user->user_type);
        $this->assertEquals($this->payload['first_name'], $user->first_name);
    }

    public function testUserCannotRegisterWithIncompleteRequiredData()
    {
        foreach ($this->payload as $key => $value) {
            if (! in_array($key, $this->requiredFields)) {
                continue;
            }

            $payload = $this->payload;
            unset($payload[$key]);

            $response = $this->postJson(route('api.candidate.auth.register'), $payload);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors($key);

            $user = User::first();
            $this->assertNull($user);
        }
    }

    public function testUserCannotRegisterWithInvalidEmail()
    {
        $this->payload['email'] = 'invalid-email-address';

        $response = $this->postJson(route('api.candidate.auth.register'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');

        $user = User::first();
        $this->assertNull($user);
    }

    public function testUserCannotRegisterWithPasswordsNotMatching()
    {
        $this->payload['password_confirmation'] = 'not-matching-password';

        $response = $this->postJson(route('api.candidate.auth.register'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('password_confirmation');

        $user = User::first();
        $this->assertNull($user);
    }

    public function testUserCannotRegisterWithInvalidDate()
    {
        $this->payload['date_of_birth'] = 'invalid-date';

        $response = $this->postJson(route('api.candidate.auth.register'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('date_of_birth');

        $user = User::first();
        $this->assertNull($user);
    }

    public function testUserCannotRegisterWithInvalidSex()
    {
        $this->payload['sex'] = 100;

        $response = $this->postJson(route('api.candidate.auth.register'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('sex');

        $user = User::first();
        $this->assertNull($user);
    }
}
