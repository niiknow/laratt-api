<?php

namespace Tests\api\Controllers;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan as Artisan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;

class TableControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $yellow = "\e[1;33m";
    private $green  = "\e[0;32m";
    private $white  = "\e[0;37m";
    private $url    = "/api/v1/tables";

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
        echo "\n\r\e[0;31mRefreshing the database for TableControllerTest...\n\r";
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

    public function testCreateUpdateDeleteBoomTable()
    {
        echo "\n\r{$this->yellow}    should create, update, and delete boom table...";

        $postData = [
            'name' => 'Tom'
        ];
        $headers  = array(
            'Accept'        => 'application/json',
            'x-tenant'      => 'utest'
        );
        $url      = $this->url . '/boom/create';

        // create
        $response = $this->post($url, $postData, $headers);
        // \Log::error(json_encode($response));
        $response->assertStatus(201);
        $body = $response->json();

        $item = \Niiknow\Laratt\Models\TableModel::query()->from('utest_boom')->where('name', $postData['name'])->first();
        $this->assertTrue(isset($item), 'Item exists.');

        $url = $this->url . '/boom/' . $item->uid . '/update';

        // update
        $postData['name'] = 'Noogen';
        $postData['uid']  = $item->uid;
        $response         = $this->post($url, $postData, $headers);

        $item = \Niiknow\Laratt\Models\TableModel::query()->from('utest_boom')->where('name', $postData['name'])->first();
        $this->assertTrue(isset($item), 'Item exists.');
        $this->assertSame('Noogen', $item->name);

        $url = $this->url . '/boom/' . $item->uid . '/delete';

        //delete
        $response = $this->post($url, [], $headers);

        $item = \Niiknow\Laratt\Models\TableModel::query()->from('utest_boom')->where('name', $postData['name'])->first();
        $this->assertTrue(!isset($item), 'Item does not exists.');

        echo " {$this->green}[OK]{$this->white}\r\n";
    }
}
