<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        /* Seed for category */
        DB::table("category")->insert([
            "name" => "Romance",
            "created_at" => date("Y-m-d H:i:s")
        ]);

        DB::table("category")->insert([
            "name" => "Action",
            "created_at" => date("Y-m-d H:i:s")
        ]);

        /* Seed for cd */
        DB::table("cd")->insert([
            "title" => "Kimi No Na Wa",
            "category_id" => 1,
            "rate" => 8000,
            "quantity" => 30,
            "created_at" => date("Y-m-d H:i:s")
        ]);

        DB::table("cd")->insert([
            "title" => "Tenki No Ko",
            "category_id" => 1,
            "rate" => 10000,
            "quantity" => 35,
            "created_at" => date("Y-m-d H:i:s")
        ]);

        DB::table("cd")->insert([
            "title" => "SWAT",
            "category_id" => 2,
            "rate" => 15000,
            "quantity" => 10,
            "created_at" => date("Y-m-d H:i:s")
        ]);

        /* Seed for user */
        DB::table("user")->insert([
            "name" => "Garry Ariel",
            "identity_type" => "KTP",
            "identity_number" => "3177102705990001",
            "phone_number" => "089611765432",
            "address" => "Jakarta Selatan",
            "created_at" => date("Y-m-d H:i:s")
        ]);

        DB::table("user")->insert([
            "name" => "Bobi Bola",
            "identity_type" => "Kartu Pelajar",
            "identity_number" => "1008957632",
            "phone_number" => "089611765421",
            "address" => "Jakarta Utara",
            "created_at" => date("Y-m-d H:i:s")
        ]);
    }
}
