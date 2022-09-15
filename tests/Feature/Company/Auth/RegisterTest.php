<?php

namespace Tests\Feature\Company\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@company.com',
        'password' => 'password',
        'password_confirmation' => 'password',

        'name' => 'Company Inc.',
        'description' => 'Company Description',
        'address' => 'Malang, East Java',
        'date_of_establishment' => '2001-01-01',
    ];

    private array $requiredFields = [
        'first_name',
        'last_name',
        'email',
        'password',
        'password_confirmation',

        'name',
        'address',
        'date_of_establishment',
    ];

    public function testUserCanRegisterWithCorrectData()
    {
        $response = $this->postJson(route('api.company.auth.register'), $this->payload);

        $response->assertSuccessful();

        $user = User::with('companyProfile')->first();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($this->payload['email'], $user->email);
        $this->assertEquals(User::TYPE_COMPANY, $user->user_type);
        $this->assertEquals($this->payload['name'], $user->companyProfile->name);
    }

    public function testUserCanRegisterWithIncompleteOptionalData()
    {
        foreach ($this->payload as $key => $value) {
            if (in_array($key, $this->requiredFields)) {
                continue;
            }

            $payload = $this->payload;
            unset($payload[$key]);

            $response = $this->postJson(route('api.company.auth.register'), $payload);

            $response->assertSuccessful();

            $user = User::first();
            $this->assertInstanceOf(User::class, $user);
            $this->assertEquals($this->payload['email'], $user->email);
            $this->assertEquals(User::TYPE_COMPANY, $user->user_type);
            $this->assertEquals($this->payload['name'], $user->companyProfile->name);
        }
    }

    public function testUserCannotRegisterWithIncompleteRequiredData()
    {
        foreach ($this->payload as $key => $value) {
            if (! in_array($key, $this->requiredFields)) {
                continue;
            }

            $payload = $this->payload;
            unset($payload[$key]);

            $response = $this->postJson(route('api.company.auth.register'), $payload);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors($key);

            $user = User::first();
            $this->assertNull($user);
        }
    }

    public function testUserCannotRegisterWithInvalidEmail()
    {
        $this->payload['email'] = 'invalid-email-address';

        $response = $this->postJson(route('api.company.auth.register'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');

        $user = User::first();
        $this->assertNull($user);
    }

    public function testUserCannotRegisterWithPasswordsNotMatching()
    {
        $this->payload['password_confirmation'] = 'not-matching-password';

        $response = $this->postJson(route('api.company.auth.register'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('password_confirmation');

        $user = User::first();
        $this->assertNull($user);
    }

    public function testUserCannotRegisterWithInvalidDate()
    {
        $this->payload['date_of_establishment'] = 'invalid-date';

        $response = $this->postJson(route('api.company.auth.register'), $this->payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('date_of_establishment');

        $user = User::first();
        $this->assertNull($user);
    }
}
