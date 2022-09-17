<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Company\JobApplication\UpdateJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobApplicationController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/company/job-application",
     *     summary="Company's job application list",
     *     tags={"Company/Job Application"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Job application status.<br>Allow multiple values, separate by comma.<br>Available values: PENDING,SHORTLISTED,INTERVIEW,HIRED,REJECTED",
     *         example="PENDING,SHORTLISTED,INTERVIEW,HIRED,REJECTED",
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

        $jobApplications = JobApplication::with([
            'jobVacancy.company',
            'jobVacancy.jobCategory',
            'candidate.user',
        ])
            ->whereHas('jobVacancy', function ($query) {
                $query->where('company_id', auth()->user()->companyProfile->id);
            })
            ->filter($request)
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());

        return $this->successResponseWithPagination(JobApplicationResource::collection($jobApplications), $jobApplications);
    }

    /**
     * @OA\Get(
     *     path="/company/job-application/{applicationId}",
     *     summary="Candidate job application",
     *     tags={"Company/Job Application"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         name="applicationId",
     *         in="path",
     *         description="Job application ID.",
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
     *     ),
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($applicationId)
    {
        if (is_numeric($applicationId)) {
            $jobApplication = JobApplication::where('id', $applicationId)->first();

            if ($jobApplication) {
                $this->authorize('view', $jobApplication);

                return $this->successResponse(new JobApplicationResource($jobApplication));
            }
        }

        return $this->notFoundResponse();
    }

    /**
     * @OA\Patch(
     *     path="/company/job-application/{applicationId}",
     *     summary="Update candidate job application status",
     *     tags={"Company/Job Application"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         name="applicationId",
     *         in="path",
     *         description="Job application ID.",
     *         required=true,
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={
     *                     "status",
     *                 },
     *                 @OA\Property(
     *                     description="Job application status.<br>Available values: PENDING,SHORTLISTED,INTERVIEW,HIRED,REJECTED",
     *                     property="status",
     *                     type="string",
     *                     example="SHORTLISTED",
     *                 ),
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
     *         response=403,
     *         description="Authorization error",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *     ),
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateJobApplicationRequest $request, $applicationId)
    {
        if (is_numeric($applicationId)) {
            $jobApplication = JobApplication::where('id', $applicationId)->first();

            if ($jobApplication) {
                $this->authorize('update', $jobApplication);

                $status = strtoupper($request->input('status'));
                $this->validateStatusChanges($jobApplication, $status);

                $jobApplication->update([
                    'application_status' => $status
                ]);

                return $this->successResponse(new JobApplicationResource($jobApplication));
            }
        }

        return $this->notFoundResponse();
    }

    private function validateStatusChanges(JobApplication $jobApplication, $status)
    {
        if ($status == $jobApplication->application_status) {
            return;
        }

        if (
            ($status == JobApplication::STATUS_SHORTLISTED && $jobApplication->application_status != JobApplication::STATUS_PENDING)
            || ($status == JobApplication::STATUS_INTERVIEW && $jobApplication->application_status != JobApplication::STATUS_SHORTLISTED)

            || ($status == JobApplication::STATUS_HIRED && $jobApplication->application_status == JobApplication::STATUS_REJECTED)
            || ($status == JobApplication::STATUS_REJECTED && $jobApplication->application_status == JobApplication::STATUS_HIRED)
        ) {
            throw ValidationException::withMessages([
                'status' => [__('validation.in', ['attribute' => 'status'])],
            ]);
        }
    }
}
