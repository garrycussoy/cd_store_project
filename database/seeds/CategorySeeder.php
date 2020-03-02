<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for category table
     * @return void
     */
    public function run()
    {
        DB::table("category")->insert([
            "name" => "Romance"
        ]);

        DB::table("category")->insert([
            "name" => "Action"
        ]);
    }
}
