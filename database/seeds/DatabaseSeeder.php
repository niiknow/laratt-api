<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // $this->call(XYZSeeder::class);
        // create 888 contact
        factory(Api\Models\DemoContact::class, 888)->create();
    }
}
