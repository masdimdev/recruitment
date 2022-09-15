<?php

namespace Tests\Feature\Company\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetHisCompanyProfile()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_COMPANY,
        ]);

        $token = $this->createCompanyToken($user);

        $response = $this->getJson(route('api.company.profile.index'), [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $this->assertEquals($user->email, $response->json('data.email'));
    }

    public function testUserCanUpdateCompanyName()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newCompanyName = 'New Company Co. Ltd.';

        $token = $this->createCompanyToken($user);

        $response = $this->postJson(route('api.company.profile.update'), [
            'name' => $newCompanyName
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newCompanyName, $user->companyProfile->name);
        $this->assertEquals($newCompanyName, $response->json('data.name'));
    }

    public function testUserCanUpdateDescription()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newDescription = 'Mus mauris vitae ultricies leo integer malesuada.';

        $token = $this->createCompanyToken($user);

        $response = $this->postJson(route('api.company.profile.update'), [
            'description' => $newDescription
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newDescription, $user->companyProfile->description);
        $this->assertEquals($newDescription, $response->json('data.description'));
    }

    public function testUserCanUpdateAddress()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newAddress = 'Bekasi, West Java, Indonesia';

        $token = $this->createCompanyToken($user);

        $response = $this->postJson(route('api.company.profile.update'), [
            'address' => $newAddress
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newAddress, $user->companyProfile->address);
        $this->assertEquals($newAddress, $response->json('data.address'));
    }

    public function testUserCanUpdateDateOfEstablishment()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newDateOfEstablishment = '2020-12-20';

        $token = $this->createCompanyToken($user);

        $response = $this->postJson(route('api.company.profile.update'), [
            'date_of_establishment' => $newDateOfEstablishment
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newDateOfEstablishment, $user->companyProfile->date_of_establishment);
        $this->assertEquals($newDateOfEstablishment, $response->json('data.date_of_establishment'));
    }

    public function testUserCannotUpdateDateOfEstablishmentWithInvalidDate()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newDateOfEstablishment = 'invalid-date';
        $currentDateOfEstablishment = $user->companyProfile->date_of_establishment;

        $token = $this->createCompanyToken($user);

        $response = $this->postJson(route('api.company.profile.update'), [
            'date_of_establishment' => $newDateOfEstablishment
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertEquals($currentDateOfEstablishment, $user->companyProfile->date_of_establishment);
    }

    public function testUserCannotUpdateDateOfEstablishmentWithFutureDate()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_COMPANY,
        ]);

        $newDateOfEstablishment = now()->addYear()->format('Y-m-d');
        $currentDateOfEstablishment = $user->companyProfile->date_of_establishment;

        $token = $this->createCompanyToken($user);

        $response = $this->postJson(route('api.company.profile.update'), [
            'date_of_establishment' => $newDateOfEstablishment
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertEquals($currentDateOfEstablishment, $user->companyProfile->date_of_establishment);
    }
}
