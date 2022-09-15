<?php

namespace App\Http\Controllers\Api\Candidate;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Candidate\Auth\LoginRequest;
use App\Http\Requests\Candidate\Auth\RegisterRequest;
use App\Models\User;

class AuthController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/candidate/auth/register",
     *     summary="Register candidate",
     *     tags={"Candidate/Auth"},
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
     *                     "phone_number",
     *                     "address",
     *                     "date_of_birth",
     *                     "sex",
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
     *                     property="phone_number",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="date_of_birth",
     *                     description="Format {YEAR}-{MONTH}-{DATE}",
     *                     type="string",
     *                     format="date",
     *                 ),
     *                 @OA\Property(
     *                     description="1: Male; 2: Female",
     *                     property="sex",
     *                     type="integer",
     *                 ),
     *                 example={
     *                     "first_name": "John",
     *                     "last_name": "Doe",
     *                     "email": "john.doe@example.com",
     *                     "password": "password",
     *                     "password_confirmation": "password",
     *                     "phone_number": "6281234567890",
     *                     "address": "Malang, East Java",
     *                     "date_of_birth": "1991-12-21",
     *                     "sex": 1,
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
     * @param \App\Http\Requests\Candidate\Auth\RegisterRequest $request
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
            'user_type' => User::TYPE_CANDIDATE,
        ]);

        $user->candidateProfile()->create([
            'phone_number' => $request->input('phone_number'),
            'address' => $request->input('address'),
            'date_of_birth' => $request->input('date_of_birth'),
            'sex' => $request->input('sex'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/candidate/auth/login",
     *     summary="Login candidate",
     *     tags={"Candidate/Auth"},
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
     * @param \App\Http\Requests\Candidate\Auth\LoginRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $user = User::with('candidateProfile')
            ->where('email', $request->input('email'))
            ->first();

        $token = $user->createToken('auth_token', ['as-candidate'])->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
