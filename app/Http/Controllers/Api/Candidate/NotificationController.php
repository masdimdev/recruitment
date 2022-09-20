<?php

namespace App\Http\Controllers\Api\Candidate;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/candidate/notification",
     *     summary="Candidate's job application list",
     *     tags={"Candidate/Notification"},
     *     security={ {"sanctum": {} }},
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

        $notifications = $request->user()
            ->candidateProfile
            ->notifications()
            ->latest()
            ->paginate($perPage);

        return $this->successResponseWithPagination(NotificationResource::collection($notifications), $notifications);
    }
}
