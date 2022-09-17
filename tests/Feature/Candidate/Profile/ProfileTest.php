<?php

namespace Tests\Feature\Candidate\Profile;

use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetHisProfile()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $token = $this->createCandidateToken($user);

        $response = $this->getJson(route('api.candidate.profile.index'), [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $this->assertEquals($user->email, $response->json('data.email'));
    }

    public function testUserCanUpdateFirstName()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newFirstName = 'Richard';
        $currentLastName = $user->last_name;

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
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
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newLastName = 'Roe';
        $currentFirstName = $user->first_name;

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
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

    public function testUserCanUpdatePhoneNumber()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newPhoneNumber = '6288811112222';

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
            'phone_number' => $newPhoneNumber
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newPhoneNumber, $user->candidateProfile->phone_number);
        $this->assertEquals($newPhoneNumber, $response->json('data.phone_number'));
    }

    public function testUserCannotUpdateWithInvalidPhoneNumber()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newPhoneNumber = 'not-a-phone-number';
        $currentPhoneNumber = $user->candidateProfile->phone_number;

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
            'phone_number' => $newPhoneNumber
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertEquals($currentPhoneNumber, $user->candidateProfile->phone_number);
    }


    public function testUserCannotUpdateWithDuplicatedPhoneNumber()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $otherUser = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newPhoneNumber = $otherUser->candidateProfile->phone_number;
        $currentPhoneNumber = $user->candidateProfile->phone_number;

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
            'phone_number' => $newPhoneNumber
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertEquals($currentPhoneNumber, $user->candidateProfile->phone_number);
    }

    public function testUserCanUpdateAddress()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newAddress = 'Bekasi, West Java, Indonesia';

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
            'address' => $newAddress
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newAddress, $user->candidateProfile->address);
        $this->assertEquals($newAddress, $response->json('data.address'));
    }

    public function testUserCanUpdateDateOfBirth()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newDateOfBirth = '1992-12-21';

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
            'date_of_birth' => $newDateOfBirth
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newDateOfBirth, $user->candidateProfile->date_of_birth);
        $this->assertEquals($newDateOfBirth, $response->json('data.date_of_birth'));
    }

    public function testUserCannotUpdateDateOfBirthWithInvalidDate()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newDateOfBirth = 'invalid-date';
        $currentDateOfBirth = $user->candidateProfile->date_of_birth;

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
            'date_of_birth' => $newDateOfBirth
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertEquals($currentDateOfBirth, $user->candidateProfile->date_of_birth);
    }

    public function testUserCannotUpdateDateOfBirthWithFutureDate()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newDateOfBirth = now()->addYear()->format('Y-m-d');
        $currentDateOfBirth = $user->candidateProfile->date_of_birth;

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
            'date_of_birth' => $newDateOfBirth
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertEquals($currentDateOfBirth, $user->candidateProfile->date_of_birth);
    }

    public function testUserCanUpdateSex()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newSex = CandidateProfile::SEX_MALE;
        if ($user->candidateProfile->sex == CandidateProfile::SEX_MALE) {
            $newSex = CandidateProfile::SEX_FEMALE;
        }
        $newSexText = __("profile.sex_{$newSex}");

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
            'sex' => $newSex
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertSuccessful();

        $user->refresh();
        $this->assertEquals($newSex, $user->candidateProfile->sex);
        $this->assertEquals($newSexText, $response->json('data.sex'));
    }

    public function testUserCannotUpdateSexWithInvalidData()
    {
        $user = User::factory()->create([
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $newSex = 999;
        $currentSex = $user->candidateProfile->sex;

        $token = $this->createCandidateToken($user);

        $response = $this->patchJson(route('api.candidate.profile.update'), [
            'date_of_birth' => $newSex
        ], [
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertUnprocessable();

        $user->refresh();
        $this->assertEquals($currentSex, $user->candidateProfile->sex);
    }
}
