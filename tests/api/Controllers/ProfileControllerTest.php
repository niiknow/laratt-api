<?php
namespace Tests\api\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
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
    private $url = '/api/v1/profile';

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
        return new \Api\Controllers\ProfileController();
    }

    public function testCreateUpdateDeleteProfile()
    {
        echo "\n\r{$this->yellow}    should create, update, and delete profile...";

        $postData = [
            'email'      => 'tom@noogen.com',
            'first_name' => 'Tom',
            'last_name'  => 'Noogen'
        ];

        $headers = [
            'Accept'   => 'application/json',
            'x-tenant' => 'utest'
        ];

        $url = $this->url . '/create';

        // create
        $response = $this->withHeaders($headers)->post($url, $postData);
        $response->assertStatus(201);
        $body = $response->json();

        $item = \Niiknow\Laratt\Models\ProfileModel::query()->from('utest$profile')->where('email', $postData['email'])->first();
        $this->assertTrue(isset($item));

        $url = $this->url . '/' . $item->uid . '/update';

        // update
        $postData['last_name'] = 'Niiknow';
        $postData['uid']       = $item->uid;
        $response              = $this->withHeaders($headers)->post($url, $postData);

        $item = \Niiknow\Laratt\Models\ProfileModel::query()->from('utest$profile')->where('email', $postData['email'])->first();
        $this->assertTrue(isset($item));
        $this->assertSame('Niiknow', $item->last_name);

        $url = $this->url . '/' . $item->uid . '/delete';

        //delete
        $response = $this->withHeaders($headers)->post($url, []);

        $item = \Niiknow\Laratt\Models\ProfileModel::query()->from('utest$profile')->where('email', $postData['email'])->first();
        $this->assertTrue(!isset($item));

        echo " {$this->green}[OK]{$this->white}\r\n";
    }

    public function testImportProfile()
    {
        echo "\n\r{$this->yellow}    import and truncate profile...";

        $headers = [
            'Accept'   => 'application/json',
            'x-tenant' => 'itest'
        ];

        $url = $this->url . '/import';

        $expected = 10;

        // secret
        \Storage::fake('local');

        $filePath = '/tmp/randomstring.csv';

        // create
        $data  = "email,password,data.x,data.y,meta.domain\n";
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $expected; $i++) {
            $fakedata = [
                'email'       => $faker->unique()->safeEmail,
                'password'    => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // \Log::error(json_encode($response));
                'data.x'      => $faker->catchPhrase,
                'data.y'      => $faker->domainName,
                'meta.domain' => $faker->domainWord
            ];

            $data .= '"' . implode('","', $fakedata) . "\"\n";
        }

        // count
        file_put_contents($filePath, $data);

        // test datatable query
        $response = $this->withHeaders($headers)->post($url, [
            'file' => new \Illuminate\Http\UploadedFile($filePath, 'test.csv', null, null, true)
        ]);
        $response->assertStatus(200);

        $count = \Niiknow\Laratt\Models\ProfileModel::query()->from('itest$profile')->count();
        $this->assertSame($expected, $count, 'Has right count.');

        $url      = $this->url . '/truncate';
        $response = $this->withHeaders($headers)->post($url);
        $response->assertStatus(200);

        $count = \Niiknow\Laratt\Models\ProfileModel::query()->from('itest$profile')->count();
        $this->assertSame(0, $count, 'Has right count.');

        $url      = $this->url . '/drop';
        $response = $this->withHeaders($headers)->post($url);
        $response->assertStatus(200);
    }

    public function testQueryProfile()
    {
        echo "\n\r{$this->yellow}    query profile...";

        $headers = [
            'Accept'   => 'application/json',
            'x-tenant' => 'ltest'
        ];

        $url      = $this->url . '/create';
        $expected = 20;

        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $expected; $i++) {
            $fakedata = [
                'email'    => $faker->unique()->safeEmail,
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm' // test list query
            ];
            // \Log::error(json_encode($response->json()));
            $response = $this->withHeaders($headers)->post($url, $fakedata);
            // Fake any disk here
        }

        // Create file
        $items = \Niiknow\Laratt\Models\ProfileModel::query()->from('ltest$profile')->get();
        $this->assertSame($expected, count($items));

        // secret
        $url      = $this->url . '/data';
        $response = $this->withHeaders($headers)->get($url);
        $response->assertStatus(200);
        $body = $response->json();

        $this->assertTrue(isset($body), 'Query response with data.');
        $this->assertSame($expected, $body['recordsTotal'], 'Correctly return datatable.');

        // Create file
        $url      = $this->url . '/query?limit=5&page=2';
        $response = $this->withHeaders($headers)->get($url);
        $response->assertStatus(200);
        $body = $response->json();

        $this->assertTrue(isset($body), 'Query response with data.');
        $this->assertSame(2, $body['current_page'], 'Correctly parse page parameter.');
        $this->assertSame(5, count($body['data']), 'Has right count.');
        $expected = \Niiknow\Laratt\Models\ProfileModel::query()->from('ltest$profile')->count() - 8;

        $url      = $this->url . '/query?filter[]=id:lte:8';
        $response = $this->withHeaders($headers)->delete($url);
        $response->assertStatus(200);
        // \Log::info($data);

        $count = \Niiknow\Laratt\Models\ProfileModel::query()->from('ltest$profile')->count();
        $this->assertSame($expected, $count, 'Has right count.');

        echo " {$this->green}[OK]{$this->white}\r\n";
    }
}
