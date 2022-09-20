<?php

namespace App\Http\Controllers\Api\Candidate;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Candidate\Profile\UpdateProfileRequest;
use App\Http\Resources\CandidateProfileResource;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class ProfileController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/candidate/profile",
     *     summary="Candidate profile",
     *     tags={"Candidate/Profile"},
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
    public function profile()
    {
        return $this->successResponse(new CandidateProfileResource(auth()->user()->candidateProfile));
    }

    /**
     * @OA\Patch(
     *     path="/candidate/profile",
     *     summary="Update candidate profile",
     *     tags={"Candidate/Profile"},
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
     *                     "phone_number": "6281234567890",
     *                     "address": "Jakarta, Indonesia",
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
     *         response=401,
     *         description="Authentication error",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
     *
     * @param \App\Http\Requests\Candidate\Profile\UpdateProfileRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user()->load('candidateProfile');

        if ($request->input('first_name')) {
            $user->first_name = $request->input('first_name');
        }

        if ($request->input('last_name')) {
            $user->last_name = $request->input('last_name');
        }

        if ($request->input('phone_number')) {
            $user->candidateProfile->phone_number = $request->input('phone_number');
        }

        if ($request->input('address')) {
            $user->candidateProfile->address = $request->input('address');
        }

        if ($request->input('date_of_birth')) {
            $user->candidateProfile->date_of_birth = $request->input('date_of_birth');
        }

        if ($request->input('sex')) {
            $user->candidateProfile->sex = $request->input('sex');
        }

        $user->candidateProfile->save();
        $user->save();

        return $this->successResponse(new CandidateProfileResource($user->candidateProfile));
    }
}
