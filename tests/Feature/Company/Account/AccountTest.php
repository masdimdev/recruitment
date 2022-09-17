<?php

namespace Tests\Feature\Company\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'email' => 'john.doe@example.com',
        'password' => 'password',
    ];

    public function testUserCanGetHisAccountDetail()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $token = $this->createCompanyToken($user);

        $response = $this->getJson(route('api.company.account.index'), [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $this->assertEquals($this->payload['email'], $response->json('data.email'));
    }

    public function testUserCanUpdateFirstName()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newFirstName = 'Richard';
        $currentLastName = $user->last_name;

        $token = $this->createCompanyToken($user);

        $response = $this->patchJson(route('api.company.account.update'), [
            'first_name' => $newFirstName
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newFirstName, $user->first_name);
        $this->assertEquals($newFirstName, $response->json('data.first_name'));
        $this->assertEquals($currentLastName, $response->json('data.last_name'));
    }

    public function testUserCanUpdateLastName()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newLastName = 'Roe';
        $currentFirstName = $user->first_name;

        $token = $this->createCompanyToken($user);

        $response = $this->patchJson(route('api.company.account.update'), [
            'last_name' => $newLastName
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newLastName, $user->last_name);
        $this->assertEquals($newLastName, $response->json('data.last_name'));
        $this->assertEquals($currentFirstName, $response->json('data.first_name'));
    }

    public function testUserCanUpdateEmail()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newEmail = 'new.email@example.com';

        $token = $this->createCompanyToken($user);

        $response = $this->patchJson(route('api.company.account.update'), [
            'email' => $newEmail,
            'current_password' => $this->payload['password'],
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newEmail, $user->email);
        $this->assertEquals($newEmail, $response->json('data.email'));
    }

    public function testUserCannotUpdateEmailWithoutCurrentPassword()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newEmail = 'new.email@example.com';

        $token = $this->createCompanyToken($user);

        $response = $this->patchJson(route('api.company.account.update'), [
            'email' => $newEmail,
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertEquals($this->payload['email'], $user->email);
    }

    public function testUserCannotUpdateEmailWithWrongCurrentPassword()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newEmail = 'new.email@example.com';

        $token = $this->createCompanyToken($user);

        $response = $this->patchJson(route('api.company.account.update'), [
            'email' => $newEmail,
            'current_password' => 'wrong-password',
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertEquals($this->payload['email'], $user->email);
    }

    public function testUserCanUpdatePassword()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newPassword = 'new-password';

        $token = $this->createCompanyToken($user);

        $response = $this->patchJson(route('api.company.account.update'), [
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword,
            'current_password' => $this->payload['password'],
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

    public function testUserCannotUpdatePasswordWithoutCurrentPassword()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newPassword = 'new-password';

        $token = $this->createCompanyToken($user);

        $response = $this->patchJson(route('api.company.account.update'), [
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword,
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertTrue(Hash::check($this->payload['password'], $user->password));
    }

    public function testUserCannotUpdatePasswordWithWrongCurrentPassword()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newPassword = 'new-password';

        $token = $this->createCompanyToken($user);

        $response = $this->patchJson(route('api.company.account.update'), [
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword,
            'current_password' => 'wrong-password',
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertTrue(Hash::check($this->payload['password'], $user->password));
    }

    public function testUserCannotUpdatePasswordWithoutNewPasswordConfirmation()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newPassword = 'new-password';

        $token = $this->createCompanyToken($user);

        $response = $this->patchJson(route('api.company.account.update'), [
            'new_password' => $newPassword,
            'current_password' => $this->payload['password'],
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertTrue(Hash::check($this->payload['password'], $user->password));
    }

    public function testUserCannotUpdatePasswordWithNewPasswordsNotMatching()
    {
        $user = User::factory()->create([
            'email' => $this->payload['email'],
            'password' => bcrypt($this->payload['password']),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newPassword = 'new-password';

        $token = $this->createCompanyToken($user);

        $response = $this->patchJson(route('api.company.account.update'), [
            'new_password' => $newPassword,
            'new_password_confirmation' => 'not-matching-new-password',
            'current_password' => $this->payload['password'],
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertTrue(Hash::check($this->payload['password'], $user->password));
    }
}
