<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Company\Profile\UpdateProfileRequest;
use App\Http\Resources\CompanyProfileResource;

class ProfileController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/company/profile",
     *     summary="Company profile",
     *     tags={"Company/Profile"},
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
        return $this->successResponse(new CompanyProfileResource(auth()->user()->companyProfile));
    }

    /**
     * @OA\Patch(
     *     path="/company/profile",
     *     summary="Update company profile",
     *     tags={"Company/Profile"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
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
     * @param \App\Http\Requests\Company\Profile\UpdateProfileRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user()->load('companyProfile');

        if ($request->input('name')) {
            $user->companyProfile->name = $request->input('name');
        }

        if ($request->input('description')) {
            $user->companyProfile->description = $request->input('description');
        }

        if ($request->input('address')) {
            $user->companyProfile->address = $request->input('address');
        }

        if ($request->input('date_of_establishment')) {
            $user->companyProfile->date_of_establishment = $request->input('date_of_establishment');
        }

        $user->companyProfile->save();
        $user->save();

        return $this->successResponse(new CompanyProfileResource($user->companyProfile));
    }
}
