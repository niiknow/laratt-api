<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $spaces        = \Api\Models\Space::allDict();
        $resources     = \Api\Models\Resource::allDict();
        $organizations = \Api\Models\Organization::allDict();

        $data = [
            'csrfToken'    => csrf_token(),
            'env'          => config('app.env'),
            'api_endpoint' => config('app.api'),
            'appName'      => config('app.name'),
            'resources'    => $resources,
            'spaces'       => $spaces,
            'orgs'         => $organizations,
            'my_org'       => $request['_my_host']
        ];

        return view('home', ['appSettings' => $data]);
    }

    public function healthCheck(Request $request)
    {
        //$output = exec('cd .. && ./webqueue.sh');
        // return $output;
        echo phpinfo();
    }
}
