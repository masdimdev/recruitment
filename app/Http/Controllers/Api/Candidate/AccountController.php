<?php

namespace App\Http\Controllers\Api\Candidate;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/candidate/account",
     *     summary="Candidate account",
     *     tags={"Candidate/Auth"},
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
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function updateAccount(User $user)
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/candidate/account/logout",
     *     summary="Logout candidate",
     *     tags={"Candidate/Auth"},
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->successResponse();
    }
}
