<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\JobVacancyResource;
use App\Models\JobVacancy;
use Illuminate\Http\Request;

class JobVacancyController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/public/job-vacancy",
     *     summary="Job vacancy list",
     *     tags={"Public"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query",
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Job type.<br>Allow multiple values, separate by comma.<br>Available values: INTERNSHIP,FULL_TIME,PART_TIME,FREELANCE",
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page",
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
        $jobs = JobVacancy::query();

        if ($request->has('q')) {
            $jobs->where('description', 'LIKE', "%%{$request->get('q')}%%");
        }

        if ($request->has('type')) {
            $type = preg_replace("/[^A-Za-z,]/", '', $request->get('type'));
            $types = explode(',', $type);
            $jobs->whereIn('job_type', $types);
        }

        $jobs = $jobs->paginate(10);

        return $this->successResponseWithPagination(JobVacancyResource::collection($jobs), $jobs);
    }
}
