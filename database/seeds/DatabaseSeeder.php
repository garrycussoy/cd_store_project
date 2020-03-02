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

        /* Seed for rent */
        DB::table("rent")->insert([
            "user_id" => 1,
            "returned" => False,
            "borrowed_time" => date("2020-02-20"),
            "total_items" => 2,
            "total_price" => 18000,
            "price_to_pay" => 0
        ]);

        DB::table("rent")->insert([
            "user_id" => 1,
            "returned" => True,
            "borrowed_time" => date("2020-02-20"),
            "returned_time" => date("2020-02-21"),
            "total_items" => 1,
            "total_price" => 8000,
            "price_to_pay" => 8000
        ]);

        DB::table("rent")->insert([
            "user_id" => 2,
            "returned" => False,
            "borrowed_time" => date("2020-02-20"),
            "total_items" => 2,
            "total_price" => 30000,
            "price_to_pay" => 0
        ]);

        /* Seed for rent detail */
        DB::table("rent_detail")->insert([
            "rent_id" => 1,
            "cd_id" => 1,
            "total_items" => 1,
            "total_price" => 8000
        ]);

        DB::table("rent_detail")->insert([
            "rent_id" => 1,
            "cd_id" => 2,
            "total_items" => 1,
            "total_price" => 10000
        ]);

        DB::table("rent_detail")->insert([
            "rent_id" => 2,
            "cd_id" => 1,
            "total_items" => 1,
            "total_price" => 8000
        ]);

        DB::table("rent_detail")->insert([
            "rent_id" => 3,
            "cd_id" => 3,
            "total_items" => 2,
            "total_price" => 30000
        ]);
    }
}
