<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Company\Auth\LoginRequest;
use App\Http\Requests\Company\Auth\RegisterRequest;
use App\Models\User;

class AuthController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/company/auth/register",
     *     summary="Register company",
     *     tags={"Company/Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={
     *                     "first_name",
     *                     "last_name",
     *                     "email",
     *                     "password",
     *                     "password_confirmation",
     *                     "name",
     *                     "address",
     *                     "date_of_establishment",
     *                 },
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     format="password",
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     description="Company name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="Format {YEAR}-{MONTH}-{DATE}",
     *                     property="date_of_establishment",
     *                     type="string",
     *                     format="date",
     *                 ),
     *                 example={
     *                     "first_name": "John",
     *                     "last_name": "Doe",
     *                     "email": "recruitment@company.com",
     *                     "password": "password",
     *                     "password_confirmation": "password",
     *                     "name": "Company Inc.",
     *                     "description": "Lorem ipsum ....",
     *                     "address": "Malang, East Java",
     *                     "date_of_establishment": "2001-12-21",
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'user_type' => User::TYPE_COMPANY,
        ]);

        $user->companyProfile()->create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'address' => $request->input('address'),
            'date_of_establishment' => $request->input('date_of_establishment'),
        ]);

        $token = $user->createToken('auth_token', ['as-company'])->plainTextToken;

        $user->load('companyProfile');

        return $this->successResponse([
            'user' => '',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/company/auth/login",
     *     summary="Login company",
     *     tags={"Company/Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={
     *                     "email",
     *                     "password",
     *                 },
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                 ),
     *                 example={
     *                     "email": "john.doe@example.com",
     *                     "password": "password",
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
     *
     * @param \App\Http\Requests\Company\Auth\LoginRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $user = User::with('companyProfile')
            ->where('email', $request->input('email'))
            ->first();

        $token = $user->createToken('auth_token', ['as-company'])->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
