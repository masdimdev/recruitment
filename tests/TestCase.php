<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createCandidateToken($user)
    {
        return $user->createToken('auth_token', ['as-candidate'])->plainTextToken;
    }

    protected function createCompanyToken($user)
    {
        return $user->createToken('auth_token', ['as-company'])->plainTextToken;
    }
}
