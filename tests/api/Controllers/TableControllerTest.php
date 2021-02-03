<?php
namespace Tests\api\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

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

    public function testCreateUpdateDeleteBoomTable()
    {
        echo "\n\r{$this->yellow}    should create, update, and delete boom table...";

        $postData = [
            'private' => 'Tom'
        ];

        $headers = [
            'Accept'   => 'application/json',
            'x-tenant' => 'utest'
        ];

        $url = $this->url . '/boom/create';

        // create
        $response = $this->withHeaders($headers)->post($url, $postData);

        // \Log::error(json_encode($response));
        $response->assertStatus(201);
        $body = $response->json();

        $item = \Niiknow\Laratt\Models\TableModel::query()->from('utest$boom')->first();
        $this->assertTrue(isset($item), 'Item exists.');

        $url = $this->url . '/boom/' . $item->uid . '/update';

        // update
        $postData['private'] = 'Noogen';
        $postData['uid']     = $item->uid;
        $response            = $this->withHeaders($headers)->post($url, $postData);

        $item = \Niiknow\Laratt\Models\TableModel::query()->from('utest$boom')->where('uid', $postData['uid'])->first();
        $this->assertTrue(isset($item), 'Item exists.');
        $this->assertSame('Noogen', $item->private);

        $url = $this->url . '/boom/' . $item->uid . '/delete';

        //delete
        $response = $this->withHeaders($headers)->post($url, []);

        $item = \Niiknow\Laratt\Models\TableModel::query()->from('utest$boom')->where('uid', $postData['uid'])->first();
        $this->assertTrue(!isset($item), 'Item does not exists.');

        echo " {$this->green}[OK]{$this->white}\r\n";
    }
}
