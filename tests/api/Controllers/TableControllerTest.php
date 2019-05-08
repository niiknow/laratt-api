<?php
namespace Tests\api\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan as Artisan;
use Tests\TestCase;

class TableControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var mixed
     */
    protected static $dbInitiated = false;

    /**
     * @var string
     */
    private $green = "\e[0;32m";

    /**
     * @var string
     */
    private $url = '/api/v1/tables';

    /**
     * @var string
     */
    private $white = "\e[0;37m";

    /**
     * @var string
     */
    private $yellow = "\e[1;33m";

    public function mockServices()
    {
        return new \Api\Controllers\UserController();
    }

    /**
     * Disclaimer:
     * the "right" way to do testing, that gives you the greatest
     * confidence your tests methods don't get subtly interdependent in
     * bug-hiding ways, is to re-seed your db before every test method, so
     * just put seeding code in plain setUp if you can afford the
     * performance penalty
     */
    public function setUp(): void
    {
        parent::setUp();

        if (!static::$dbInitiated) {
            static::$dbInitiated = true;
            static::initDB();
        }

        Carbon::setTestNow(Carbon::now('UTC'));
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    public function testCreateUpdateDeleteBoomTable()
    {
        echo "\n\r{$this->yellow}    should create, update, and delete boom table...";

        $postData = [
            'name' => 'Tom'
        ];

        $headers = [
            'Accept'   => 'application/json',
            'x-tenant' => 'utest'
        ];

        $url = $this->url . '/boom/create';

        // create
        $response = $this->post($url, $postData, $headers);
        // \Log::error(json_encode($response));
        $response->assertStatus(201);
        $body = $response->json();

        $item = \Niiknow\Laratt\Models\TableModel::query()->from('utest$boom')->where('name', $postData['name'])->first();
        $this->assertTrue(isset($item), 'Item exists.');

        $url = $this->url . '/boom/' . $item->uid . '/update';

        // update
        $postData['name'] = 'Noogen';
        $postData['uid']  = $item->uid;
        $response         = $this->post($url, $postData, $headers);

        $item = \Niiknow\Laratt\Models\TableModel::query()->from('utest$boom')->where('name', $postData['name'])->first();
        $this->assertTrue(isset($item), 'Item exists.');
        $this->assertSame('Noogen', $item->name);

        $url = $this->url . '/boom/' . $item->uid . '/delete';

        //delete
        $response = $this->post($url, [], $headers);

        $item = \Niiknow\Laratt\Models\TableModel::query()->from('utest$boom')->where('name', $postData['name'])->first();
        $this->assertTrue(!isset($item), 'Item does not exists.');

        echo " {$this->green}[OK]{$this->white}\r\n";
    }

    protected static function initDB()
    {
        echo "\n\r\e[0;31mRefreshing the database for TableControllerTest...\n\r";
        Artisan::call('migrate:fresh');
    }
}
