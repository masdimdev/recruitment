<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\JobCategoryResource;
use App\Models\JobCategory;

class JobCategoryController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/public/job-category",
     *     summary="Job category list",
     *     tags={"Public"},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->successResponse(JobCategoryResource::collection(JobCategory::all()));
    }
}
