<?php

namespace Tests\api\Controllers;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan as Artisan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $yellow = "\e[1;33m";
    private $green  = "\e[0;32m";
    private $white  = "\e[0;37m";
    private $url    = "/api/v1/users";

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
        echo "\n\r\e[0;31mRefreshing the database for UserControllerTest...\n\r";
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
        return new \Api\Controllers\UserController();
    }

    public function testCreateUpdateDeleteUser()
    {
        echo "\n\r{$this->yellow}    should create, update, and delete user...";

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

        $user = \Api\Models\User::query()->from('utest_user')->where('email', $postData['email'])->first();
        $this->assertTrue(isset($user));

        $url = $this->url . '/' . $user->id . '/update';

        // update
        $postData['last_name'] = 'Niiknow';
        $postData['id']        = $user->id;
        $response              = $this->post($url, $postData, $headers);

        $user = \Api\Models\User::query()->from('utest_user')->where('email', $postData['email'])->first();
        $this->assertTrue(isset($user));
        $this->assertSame('Niiknow', $user->last_name);

        $url = $this->url . '/' . $user->id . '/delete';

        //delete
        $response = $this->post($url, [], $headers);

        $user = \Api\Models\User::query()->from('utest_user')->where('email', $postData['email'])->first();
        $this->assertTrue(!isset($user));

        echo " {$this->green}[OK]{$this->white}\r\n";
    }

/*
    public function testQueryUser()
    {
        echo "\n\r{$this->yellow}    query user...";

        for ($x = 0; $x < 10; $x++) {
            User::create([]);
        }

        echo " {$this->green}[OK]{$this->white}\r\n";
    }*/
}
