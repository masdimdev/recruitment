<?php

namespace Tests\Feature\General;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CandidateProfileTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetCandidatePublicProfile()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $response = $this->getJson(route('api.public.candidate.profile', ['candidateId' => $user->candidateProfile->id]));

        $response->assertSuccessful();

        $this->assertEquals($user->email, $response->json('data.email'));
    }

    public function testUserCannotGetInvalidCandidatePublicProfile()
    {
        $response = $this->getJson(route('api.public.candidate.profile', ['candidateId' => 'invalid-id']));

        $response->assertNotFound();
    }
}
