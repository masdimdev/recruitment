<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/company/account",
     *     summary="Company account",
     *     tags={"Company/Auth"},
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
     *     path="/company/account/logout",
     *     summary="Logout company",
     *     tags={"Company/Auth"},
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
