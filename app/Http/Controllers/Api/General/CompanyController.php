<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CompanyProfileResource;
use App\Http\Resources\JobVacancyResource;
use App\Models\CompanyProfile;
use App\Models\JobVacancy;
use Illuminate\Http\Request;

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
     *         response=404,
     *         description="Resource not found",
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($companyId)
    {
        if (is_numeric($companyId)) {
            $companyProfile = CompanyProfile::find($companyId);

            if ($companyProfile) {
                return $this->successResponse(new CompanyProfileResource($companyProfile));
            }
        }

        return $this->notFoundResponse();
    }

    /**
     * @OA\Get(
     *     path="/public/company/{companyId}/job-vacancy",
     *     summary="List of job vacancies from the company",
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
     *         response=404,
     *         description="Resource not found",
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function jobVacancy(Request $request, $companyId)
    {
        if (is_numeric($companyId)) {
            $companyProfile = CompanyProfile::find($companyId);

            if ($companyProfile) {
                $perPage = intval($request->query('per-page', 10));
                $perPage = max(min($perPage, 25), 1); // Maximum 25 data per-page, minimum 1 data per-page.

                $jobs = JobVacancy::with('company', 'jobCategory')
                    ->where('company_id', $companyId)
                    ->where('is_active', true)
                    ->filter($request)
                    ->latest()
                    ->paginate($perPage)
                    ->appends($request->query());

                return $this->successResponseWithPagination(JobVacancyResource::collection($jobs), $jobs);
            }
        }

        return $this->notFoundResponse();
    }
}
