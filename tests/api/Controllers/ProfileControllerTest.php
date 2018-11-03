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
    private $url    = "/api/v1/profile";

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

        $item = \Niiknow\Laratt\Models\ProfileModel::query()->from('utest_profile')->where('email', $postData['email'])->first();
        $this->assertTrue(isset($item));

        $url = $this->url . '/' . $item->uid . '/update';

        // update
        $postData['last_name'] = 'Niiknow';
        $postData['uid']       = $item->uid;
        $response              = $this->post($url, $postData, $headers);

        $item = \Niiknow\Laratt\Models\ProfileModel::query()->from('utest_profile')->where('email', $postData['email'])->first();
        $this->assertTrue(isset($item));
        $this->assertSame('Niiknow', $item->last_name);

        $url = $this->url . '/' . $item->uid . '/delete';

        //delete
        $response = $this->post($url, [], $headers);

        $item = \Niiknow\Laratt\Models\ProfileModel::query()->from('utest_profile')->where('email', $postData['email'])->first();
        $this->assertTrue(!isset($item));

        echo " {$this->green}[OK]{$this->white}\r\n";
    }

    public function testQueryProfile()
    {
        echo "\n\r{$this->yellow}    query profile...";

        $headers  = array(
            'Accept'        => 'application/json',
            'x-tenant'      => 'ltest'
        );
        $url      = $this->url . '/create';
        $expected = 20;

        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $expected; $i++) {
            $fakedata = [
                'email' => $faker->unique()->safeEmail,
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
            ];
            // create
            $response = $this->post($url, $fakedata, $headers);
            // \Log::error(json_encode($response));
        }

        // count
        $items =  \Niiknow\Laratt\Models\ProfileModel::query()->from('ltest_profile')->get();
        $this->assertSame($expected, count($items));

        // test datatable query
        $url      = $this->url . '/data';
        $response = $this->withHeaders($headers)->get($url);
        $response->assertStatus(200);
        $body = $response->json();

        $this->assertTrue(isset($body), "Query response with data.");
        $this->assertSame($expected, $body['recordsTotal'], "Correctly return datatable.");

        // test list query
        $url      = $this->url . '/list?limit=5&page=2';
        $response = $this->withHeaders($headers)->get($url);
        $response->assertStatus(200);
        $body = $response->json();

        $this->assertTrue(isset($body), "Query response with data.");
        $this->assertSame(2, $body['current_page'], "Correctly parse page parameter.");
        $this->assertSame(5, count($body['data']), "Has right count.");
        $expected = \Niiknow\Laratt\Models\ProfileModel::query()->from('ltest_profile')->count() - 8;

        $url      = $this->url . '/list?filter[]=id:lte:8';
        $response = $this->withHeaders($headers)->delete($url);
        $response->assertStatus(200);
        // \Log::error(json_encode($response->json()));

        $count = \Niiknow\Laratt\Models\ProfileModel::query()->from('ltest_profile')->count();
        $this->assertSame($expected, $count, "Has right count.");

        echo " {$this->green}[OK]{$this->white}\r\n";
    }

    public function testImportProfile()
    {
        echo "\n\r{$this->yellow}    import and truncate profile...";

        $headers  = array(
            'Accept'        => 'application/json',
            'x-tenant'      => 'itest'
        );
        $url      = $this->url . '/import';
        $expected = 10;

        // Fake any disk here
        \Storage::fake('local');

        $filePath = '/tmp/randomstring.csv';

        // Create file
        $data  = "email,password,data.x,data.y,meta.domain\n";
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $expected; $i++) {
            $fakedata = [
                'email' => $faker->unique()->safeEmail,
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
                'data.x' => $faker->catchPhrase,
                'data.y' => $faker->domainName,
                'meta.domain' => $faker->domainWord
            ];

            $data .= '"' . join($fakedata, '","') . "\"\n";
        }

        // Create file
        file_put_contents($filePath, $data);

        // \Log::info($data);
        $response = $this->withHeaders($headers)->post($url, [
            'file' => new \Illuminate\Http\UploadedFile($filePath, 'test.csv', null, null, null, true),
        ]);
        $response->assertStatus(200);

        $count = \Niiknow\Laratt\Models\ProfileModel::query()->from('itest_profile')->count();
        $this->assertSame($expected, $count, "Has right count.");

        $url      = $this->url . '/truncate';
        $response = $this->withHeaders($headers)->post($url);
        $response->assertStatus(200);

        $count = \Niiknow\Laratt\Models\ProfileModel::query()->from('itest_profile')->count();
        $this->assertSame(0, $count, "Has right count.");

        $url      = $this->url . '/drop';
        $response = $this->withHeaders($headers)->post($url);
        $response->assertStatus(200);
    }
}
