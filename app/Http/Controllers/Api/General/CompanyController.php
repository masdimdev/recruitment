<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CompanyProfileResource;
use App\Models\CompanyProfile;

class CompanyController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/public/company/{companyId}",
     *     summary="Company public profile",
     *     tags={"Public"},
     *     @OA\Parameter(
     *         name="companyId",
     *         in="path",
     *         description="Company ID.",
     *         required=true,
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
     *         response=404,
     *         description="Resource not found",
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile($companyId)
    {
        if (is_numeric($companyId)) {
            $companyProfile = CompanyProfile::find($companyId);

            if ($companyProfile) {
                return $this->successResponse(new CompanyProfileResource($companyProfile));
            }
        }

        return $this->notFoundResponse();
    }
}
