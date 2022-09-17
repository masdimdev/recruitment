<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CandidateProfileResource;
use App\Models\CandidateProfile;

class CandidateController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/public/candidate/{candidateId}",
     *     summary="Candidate public profile",
     *     tags={"Public"},
     *     @OA\Parameter(
     *         name="candidateId",
     *         in="path",
     *         description="Candidate ID.",
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
    public function show($candidateId)
    {
        if (is_numeric($candidateId)) {
            $candidateProfile = CandidateProfile::find($candidateId);

            if ($candidateProfile) {
                return $this->successResponse(new CandidateProfileResource($candidateProfile));
            }
        }

        return $this->notFoundResponse();
    }
}
