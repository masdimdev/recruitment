<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="OpenApi Documentation",
 *     @OA\Contact(
 *         name="Dimas",
 *         url="https://masdim.dev",
 *         email="me@masdim.dev"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=API_URL
 * )
 *
 */
class ApiController extends Controller
{
    use ApiResponse;
}