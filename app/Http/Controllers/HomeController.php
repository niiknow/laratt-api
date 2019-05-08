<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * @param  Request $request
     * @return mixed
     */
    public function healthCheck(Request $request)
    {
        $output = exec('cd .. && ./webqueue.sh');

        return $output;
        // echo phpinfo();
    }

    /**
     * @param Request $request
     */
    public function home(Request $request)
    {
        $data = [
            'csrfToken'    => csrf_token(),
            'env'          => config('app.env'),
            'appName'      => config('app.name'),
            'api_endpoint' => config('admin.api')
        ];

        return view('home', ['appSettings' => $data]);
    }
}
