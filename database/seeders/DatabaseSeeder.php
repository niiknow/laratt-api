<?php
namespace Database\Seeders;

use Api\Models\DemoContact;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DemoContact::factory(888)->create();
    }
}
