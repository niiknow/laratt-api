<?php

namespace Api\Controllers;

use Carbon\Carbon;

trait ApiResponseTrait
{
    public function rsp($code, $rsp = null)
    {
        if ($code == 404) {
            return response()->json([ "error" => "not found" ], 404);
        }

        if ($code == 422) {
            return response()->json([ "error" => $rsp ]);
        }

        return response()->json($rsp, $code);
    }
}
