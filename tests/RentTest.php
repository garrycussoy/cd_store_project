<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RentTest extends TestCase
{   
    /**
     * Test GET /rent endpoint
     * Case: Success get all transactions without filter
     */
    public function testGetAllTransactionsWithoutFilter()
    {
        $this->artisan('migrate:refresh');
        $this->artisan('db:seed');

        $this->get('/rent');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([[
            "id",
            "user_id",
            "name",
            "returned",
            "borrowed_time",
            "returned_time",
            "total_items",
            "total_price",
            "price_to_pay",
            "rent_detail"
        ]]);
    }

    /**
     * Test GET /rent endpoint
     * Case: Success get all transactions with filter
     */
    public function testGetAllTransactionsWithFilter()
    {
        $parameters = [
            "name" => "Garry Ariel",
            "returned" => True
        ];

        $this->get('/rent', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([[
            "id",
            "user_id",
            "name",
            "returned",
            "borrowed_time",
            "returned_time",
            "total_items",
            "total_price",
            "price_to_pay",
            "rent_detail"
        ]]);
    }

    /**
     * Test GET /rent/{id} endpoint
     * Case: Success get a transaction by ID
     */
    public function testGetTransactionByIDSuccess()
    {
        $parameters = [
            "name" => "Garry Ariel",
            "returned" => True
        ];

        $this->get('/rent/1', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "id",
            "user_id",
            "name",
            "returned",
            "borrowed_time",
            "returned_time",
            "total_items",
            "total_price",
            "price_to_pay",
            "rent_detail"
        ]);
    }

    /**
     * Test PUT /rent endpoint
     * Case: End a transaction success
     */
    public function testEndATransactionSuccess()
    {
        $this->put('/rent/1');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "rent"
        ]);
    }
}
