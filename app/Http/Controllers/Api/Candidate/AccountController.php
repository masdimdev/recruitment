<?php

namespace App\Http\Controllers\Api\Candidate;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Candidate\Account\UpdateAccountRequest;
use App\Http\Resources\UserResource;

class AccountController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/candidate/account",
     *     summary="Candidate account",
     *     tags={"Candidate/Account"},
     *     security={ {"sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication error",
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function account()
    {
        return $this->successResponse(new UserResource(auth()->user()));
    }

    /**
     * @OA\Patch(
     *     path="/candidate/account",
     *     summary="Update candidate account",
     *     tags={"Candidate/Account"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
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
     *                     property="new_password",
     *                     type="string",
     *                     format="password",
     *                 ),
     *                 @OA\Property(
     *                     property="new_password_confirmation",
     *                     type="string",
     *                     format="password",
     *                 ),
     *                 @OA\Property(
     *                     property="current_password",
     *                     type="string",
     *                     format="password",
     *                 ),
     *                 example={
     *                     "first_name": "John",
     *                     "last_name": "Doe",
     *                     "email": "john.doe@example.com",
     *                     "new_password": "new-password",
     *                     "new_password_confirmation": "new-password",
     *                     "current_password": "password",
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication error",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
     *
     * @param \App\Http\Requests\Candidate\Account\UpdateAccountRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateAccountRequest $request)
    {
        $user = $request->user();

        if ($request->input('first_name')) {
            $user->first_name = $request->input('first_name');
        }

        if ($request->input('last_name')) {
            $user->last_name = $request->input('last_name');
        }

        if ($request->input('email')) {
            $user->email = $request->input('email');
        }

        if ($request->input('new_password')) {
            $user->password = bcrypt($request->input('new_password'));
        }

        $user->save();

        return $this->successResponse(new UserResource($user));
    }

    /**
     * @OA\Post(
     *     path="/candidate/account/logout",
     *     summary="Logout candidate",
     *     tags={"Candidate/Account"},
     *     security={ {"sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication error",
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->successResponse();
    }
}
