<?php

namespace Api\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="Table API",
 *   description="A rest-ful API supporting multi-tenant dynamic table manipulations",
 *   @OA\Contact(
 *      email="friends@niiknow.org"
 *   ),
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
