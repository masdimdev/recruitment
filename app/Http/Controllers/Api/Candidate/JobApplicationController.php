<?php

namespace App\Http\Controllers\Api\Candidate;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Candidate\JobApplication\StoreJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/candidate/job-application",
     *     summary="Candidate's job application list",
     *     tags={"Candidate/Job Application"},
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
            ->whereHas('candidate', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->filter($request)
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());

        return $this->successResponseWithPagination(JobApplicationResource::collection($jobApplications), $jobApplications);
    }

    /**
     * @OA\Post(
     *     path="/candidate/job-application",
     *     summary="Post new job application",
     *     tags={"Candidate/Job Application"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={
     *                     "job_vacancy_id",
     *                 },
     *                 @OA\Property(
     *                     property="cover_letter",
     *                     type="string",
     *                     example="Perferendis molestias sunt sunt. Omnis rerum architecto repellat sint.",
     *                 ),
     *                 @OA\Property(
     *                     property="job_vacancy_id",
     *                     type="string",
     *                 ),
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
    public function store(StoreJobApplicationRequest $request)
    {
        $jobVacancy = JobApplication::where('job_vacancy_id', $request->input('job_vacancy_id'))
            ->where('candidate_id', $request->user()->candidateProfile->id)
            ->whereIn('application_status', [
                JobApplication::STATUS_PENDING,
                JobApplication::STATUS_SHORTLISTED,
                JobApplication::STATUS_INTERVIEW,
            ])
            ->first();

        if (! $jobVacancy) {
            $jobVacancy = JobApplication::create([
                'cover_letter' => $request->input('cover_letter'),
                'application_status' => JobApplication::STATUS_PENDING,
                'job_vacancy_id' => $request->input('job_vacancy_id'),
                'candidate_id' => $request->user()->candidateProfile->id,
            ]);
        }

        return $this->successResponse(new JobApplicationResource($jobVacancy));
    }

    /**
     * @OA\Get(
     *     path="/candidate/job-application/{applicationId}",
     *     summary="Candidate job application",
     *     tags={"Candidate/Job Application"},
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
}
