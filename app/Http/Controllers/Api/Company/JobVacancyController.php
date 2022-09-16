<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Company\JobVacancy\StoreJobVacancyRequest;
use App\Http\Requests\Company\JobVacancy\UpdateJobVacancyRequest;
use App\Http\Resources\JobVacancyResource;
use App\Models\JobVacancy;
use Illuminate\Http\Request;

class JobVacancyController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/company/job-vacancy",
     *     summary="Job vacancy list",
     *     tags={"Company/Job Vacancy"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query",
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Job type.<br>Allow multiple values, separate by comma.<br>Available values: INTERNSHIP, FULL_TIME, PART_TIME, FREELANCE",
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page",
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
        $jobs = JobVacancy::query();

        if ($request->has('q')) {
            $jobs->where('description', 'LIKE', "%%{$request->get('q')}%%");
        }

        if ($request->has('type')) {
            $type = preg_replace("/[^A-Za-z,]/", '', $request->get('type'));
            $types = explode(',', $type);
            $jobs->whereIn('job_type', $types);
        }

        $jobs->where('company_id', $request->user()->companyProfile->id);

        $jobs = $jobs->paginate(10);

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
     * @OA\Get(
     *     path="/company/job-vacancy/{jobId}",
     *     summary="Show job vacancy",
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
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $jobId)
    {
        if (is_numeric($jobId)) {
            $jobVacancy = JobVacancy::where('company_id', $request->user()->companyProfile->id)
                ->where('id', $jobId)
                ->first();

            if ($jobVacancy) {
                return $this->successResponse(new JobVacancyResource($jobVacancy));
            }
        }

        return $this->notFoundResponse();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Company\JobVacancy\UpdateJobVacancyRequest $request
     * @param \App\Models\JobVacancy                                        $jobVacancy
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJobVacancyRequest $request, JobVacancy $jobVacancy)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\JobVacancy $jobVacancy
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobVacancy $jobVacancy)
    {
        //
    }
}
