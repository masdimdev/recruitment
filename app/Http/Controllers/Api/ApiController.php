<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;

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