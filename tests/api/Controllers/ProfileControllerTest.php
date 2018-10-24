<?php

namespace Tests\api\Controllers;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan as Artisan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;

class ProfileControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $yellow = "\e[1;33m";
    private $green  = "\e[0;32m";
    private $white  = "\e[0;37m";
    private $url    = "/api/v1/profiles";

    /**
     * Disclaimer:
     * the "right" way to do testing, that gives you the greatest
     * confidence your tests methods don't get subtly interdependent in
     * bug-hiding ways, is to re-seed your db before every test method, so
     * just put seeding code in plain setUp if you can afford the
     * performance penalty
     */

    protected static $dbInitiated = false;

    protected static function initDB()
    {
        echo "\n\r\e[0;31mRefreshing the database for ProfileControllerTest...\n\r";
        Artisan::call('migrate:fresh');
    }

    public function setUp()
    {
        parent::setUp();

        if (!static::$dbInitiated) {
            static::$dbInitiated = true;
            static::initDB();
        }

        Carbon::setTestNow(Carbon::now('UTC'));
    }

    public function tearDown()
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    public function mockServices()
    {
        return new \Api\Controllers\ProfileController();
    }

    public function testCreateUpdateDeleteProfile()
    {
        echo "\n\r{$this->yellow}    should create, update, and delete profile...";

        $postData = [
            'email'         => 'tom@noogen.com',
            'first_name'    => 'Tom',
            'last_name'     => 'Noogen'
        ];
        $headers  = array(
            'Accept'        => 'application/json',
            'x-tenant'      => 'utest'
        );
        $url      = $this->url . '/create';

        // create
        $response = $this->post($url, $postData, $headers);
        $response->assertStatus(201);
        $body = $response->json();

        $item = \Api\Models\Profile::query()->from('utest_profile')->where('email', $postData['email'])->first();
        $this->assertTrue(isset($item));

        $url = $this->url . '/' . $item->uid . '/update';

        // update
        $postData['last_name'] = 'Niiknow';
        $postData['uid']       = $item->uid;
        $response              = $this->post($url, $postData, $headers);

        $item = \Api\Models\Profile::query()->from('utest_profile')->where('email', $postData['email'])->first();
        $this->assertTrue(isset($item));
        $this->assertSame('Niiknow', $item->last_name);

        $url = $this->url . '/' . $item->uid . '/delete';

        //delete
        $response = $this->post($url, [], $headers);

        $item = \Api\Models\Profile::query()->from('utest_profile')->where('email', $postData['email'])->first();
        $this->assertTrue(!isset($item));

        echo " {$this->green}[OK]{$this->white}\r\n";
    }

    public function testQueryProfile()
    {
        echo "\n\r{$this->yellow}    query profile...";

        $headers = array(
            'Accept'        => 'application/json',
            'x-tenant'      => 'utest'
        );
        $url     = $this->url . '/create';


        // call create 20 times
        factory(\Api\Models\Profile::class, 20)->make()->each(function ($u) use ($url, $headers) {
            // create
            $response = $this->post($url, $u->toArray(), $headers);
            // \Log::error(json_encode($response));
        });

        // count 20
        $items = \Api\Models\Profile::query()->from('utest_profile')->get();
        $this->assertSame(20, count($items));

        // perform query
        $headers  = array(
            'Accept'        => 'application/json',
            'x-tenant'      => 'utest'
        );
        $url      = $this->url . '?limit=5&page=2';
        $response = $this->withHeaders($headers)->get($url);
        $response->assertStatus(200);
        $body = $response->json();

        $this->assertTrue(isset($body), "Query response with data.");
        $this->assertSame(2, $body['current_page'], "Correctly parse page parameter.");
        $this->assertSame(5, count($body['data']), "Has right count.");

        echo " {$this->green}[OK]{$this->white}\r\n";
    }
}
