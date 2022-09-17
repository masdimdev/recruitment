<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Company\JobVacancy\StoreJobVacancyRequest;
use App\Http\Requests\Company\JobVacancy\UpdateJobVacancyRequest;
use App\Http\Resources\JobApplicationResource;
use App\Http\Resources\JobVacancyResource;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use Illuminate\Http\Request;

class JobVacancyController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/company/job-vacancy",
     *     summary="Company's job vacancy list",
     *     tags={"Company/Job Vacancy"},
     *     security={ {"sanctum": {} }},
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
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication error",
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
            ->where('company_id', $request->user()->companyProfile->id)
            ->filter($request)
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());

        return $this->successResponseWithPagination(JobVacancyResource::collection($jobs), $jobs);
    }

    /**
     * @OA\Post(
     *     path="/company/job-vacancy",
     *     summary="Post new job vacancy",
     *     tags={"Company/Job Vacancy"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={
     *                     "name",
     *                     "description",
     *                     "is_active",
     *                     "job_type",
     *                     "job_category_id",
     *                 },
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="Job type.<br>Available values: INTERNSHIP, FULL_TIME, PART_TIME, FREELANCE",
     *                     property="job_type",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="job_category_id",
     *                     type="string",
     *                 ),
     *                 example={
     *                     "name": "Software Engineer",
     *                     "description": "Backend Software Engineer",
     *                     "is_active": true,
     *                     "job_type": "FULL_TIME",
     *                     "job_category_id": "1",
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreJobVacancyRequest $request)
    {
        $jobVacancy = JobVacancy::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'is_active' => $request->input('is_active'),
            'job_type' => $request->input('job_type'),
            'job_category_id' => $request->input('job_category_id'),
            'company_id' => $request->user()->companyProfile->id,
        ]);

        return $this->successResponse(new JobVacancyResource($jobVacancy));
    }

    /**
     * @OA\Patch(
     *     path="/company/job-vacancy/{jobId}",
     *     summary="Update existing job vacancy",
     *     tags={"Company/Job Vacancy"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         name="jobId",
     *         in="path",
     *         description="Job vacancy ID.",
     *         required=true,
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={
     *                     "name",
     *                     "description",
     *                     "is_active",
     *                     "job_type",
     *                     "job_category_id",
     *                 },
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="Job type.<br>Available values: INTERNSHIP, FULL_TIME, PART_TIME, FREELANCE",
     *                     property="job_type",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="job_category_id",
     *                     type="string",
     *                 ),
     *                 example={
     *                     "name": "Software Engineer",
     *                     "description": "Backend Software Engineer",
     *                     "is_active": true,
     *                     "job_type": "FULL_TIME",
     *                     "job_category_id": "1",
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication error",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
     *
     * @param \App\Http\Requests\Company\JobVacancy\UpdateJobVacancyRequest $request
     * @param                                                               $jobId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateJobVacancyRequest $request, $jobId)
    {
        if (is_numeric($jobId)) {
            $jobVacancy = JobVacancy::where('id', $jobId)->first();

            if ($jobVacancy) {
                $this->authorize('update', $jobVacancy);

                $jobVacancy->update([
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'is_active' => $request->input('is_active'),
                    'job_type' => $request->input('job_type'),
                    'job_category_id' => $request->input('job_category_id'),
                ]);

                return $this->successResponse(new JobVacancyResource($jobVacancy));
            }
        }

        return $this->notFoundResponse();
    }

    /**
     * @OA\Delete(
     *     path="/company/job-vacancy/{jobId}",
     *     summary="Delete existing job vacancy",
     *     tags={"Company/Job Vacancy"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         name="jobId",
     *         in="path",
     *         description="Job vacancy ID.",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication error",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *     )
     * )
     *
     * @param $jobId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($jobId)
    {
        if (is_numeric($jobId)) {
            $jobVacancy = JobVacancy::where('id', $jobId)->first();

            if ($jobVacancy) {
                $this->authorize('delete', $jobVacancy);

                $jobVacancy->delete();

                return $this->successResponse();
            }
        }

        return $this->notFoundResponse();
    }

    /**
     * @OA\Get(
     *     path="/company/job-vacancy/{jobId}/application",
     *     summary="Show job vacancy's application list",
     *     tags={"Company/Job Vacancy"},
     *     security={ {"sanctum": {} }},
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
     *         response=401,
     *         description="Authentication error",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Authorization error",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function jobApplication(Request $request, $jobId)
    {
        if (is_numeric($jobId)) {
            $jobVacancy = JobVacancy::where('id', $jobId)->first();

            if ($jobVacancy) {
                $perPage = intval($request->query('per-page', 10));
                $perPage = max(min($perPage, 25), 1); // Maximum 25 data per-page, minimum 1 data per-page.

                $jobApplications = JobApplication::with([
                    'jobVacancy.company',
                    'jobVacancy.jobCategory',
                    'candidate.user',
                ])
                    ->whereHas('jobVacancy', function ($query) {
                        $query->where('company_id', auth()->user()->companyProfile->id);
                    })
                    ->where('job_vacancy_id', $jobId)
                    ->filter($request)
                    ->latest()
                    ->paginate($perPage)
                    ->appends($request->query());

                return $this->successResponseWithPagination(JobApplicationResource::collection($jobApplications), $jobApplications);
            }
        }

        return $this->notFoundResponse();
    }
}
