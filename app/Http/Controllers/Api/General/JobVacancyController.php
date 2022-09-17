<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\JobVacancyResource;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class JobVacancyController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/public/job-vacancy",
     *     summary="Job vacancy list",
     *     tags={"Public"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search query",
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Job type.<br>Allow multiple values, separate by comma.<br>Available values: INTERNSHIP,FULL_TIME,PART_TIME,FREELANCE",
     *         example="INTERNSHIP,FULL_TIME,PART_TIME,FREELANCE",
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Job category ID.<br>Allow multiple values, separate by comma.",
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page",
     *         example="1",
     *     ),
     *     @OA\Parameter(
     *         name="per-page",
     *         in="query",
     *         description="Number of data on a single page",
     *         example="10",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = intval($request->query('per-page', 10));
        $perPage = max(min($perPage, 25), 1); // Maximum 25 data per-page, minimum 1 data per-page.

        $jobs = JobVacancy::with('company', 'jobCategory')
            ->where('is_active', true)
            ->filter($request)
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());

        return $this->successResponseWithPagination(JobVacancyResource::collection($jobs), $jobs);
    }

    /**
     * @OA\Get(
     *     path="/public/job-vacancy/{jobId}",
     *     summary="Show job vacancy",
     *     tags={"Public"},
     *     @OA\Parameter(
     *         name="jobId",
     *         in="path",
     *         description="Job vacancy ID.",
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
    public function show($jobId)
    {
        if (is_numeric($jobId)) {
            $jobVacancy = JobVacancy::where('id', $jobId)->first();

            if ($jobVacancy) {
                return $this->successResponse(new JobVacancyResource($jobVacancy));
            }
        }

        return $this->notFoundResponse();
    }
}
