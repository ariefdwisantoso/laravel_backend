<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

// class Controller extends BaseController
// {
//     use AuthorizesRequests, ValidatesRequests;
// }

/**
 * @OA\Info(
 *    title="Your super  ApplicationAPI",
 *    version="1.0.0",
 * )
 * @OA\Get(
 *    path="/",
 *    description="Home page",
 *    @OA\Response(response="default", description="welcome")
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}