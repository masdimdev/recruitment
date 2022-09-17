<?php

namespace Tests\Feature\General;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetCompanyPublicProfile()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_COMPANY,
        ]);

        $response = $this->getJson(route('api.public.company.show', ['companyId' => $user->companyProfile->id]));

        $response->assertSuccessful();

        $this->assertEquals($user->companyProfile->name, $response->json('data.name'));
    }

    public function testUserCannotGetInvalidCompanyPublicProfile()
    {
        $response = $this->getJson(route('api.public.company.show', ['companyId' => 'invalid-id']));

        $response->assertNotFound();
    }
}
