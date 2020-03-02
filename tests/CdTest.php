<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CdTest extends TestCase
{   
    /**
     * Test GET /cd endpoint
     * Case: Success get all categories without filter
     */
    public function testGetAllCategoriesWithoutFilter()
    {
        $this->artisan('migrate:refresh');
        $this->artisan('db:seed');

        $this->get('/cd');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([["id", "category_id", "category", "title", "rate", "quantity"]]);
    }

    /**
     * Test GET /cd endpoint
     * Case: Success get all categories with filter
     */
    public function testGetAllCategoriesWithFilter()
    {
        $parameters = [
            "title" => "No",
            "category" => "Romance",
            "min_price" => 8000,
            "max_price" => 15000,
            "min_quantity" => 30,
            "max_quantity" => 35
        ];

        $this->get('/cd', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([["id", "category_id", "category", "title", "rate", "quantity"]]);
    }

    /**
     * Test POST /cd endpoint
     * Case: Success insert new CD
     */
    public function testInsertNewCDSuccess()
    {
        $parameters = [
            "title" => "New Police Story",
            "category_id" => 2,
            "rate" => 12000,
            "quantity" => 8
        ];

        $this->post('/cd', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "cd" => ["id", "category_id", "category", "title", "rate", "quantity"]
        ]);
    }

    /**
     * Test POST /cd endpoint
     * Case: Duplicate CD
     */
    public function testInsertNewCDDuplicate()
    {
        $parameters = [
            "title" => "New Police Story",
            "category_id" => 2,
            "rate" => 12000,
            "quantity" => 8
        ];

        $this->post('/cd', $parameters);
        $this->seeStatusCode(409);
        $this->seeJsonStructure(["message"]);
    }

    /**
     * Test POST /cd endpoint
     * Case: Same name but different category
     */
    public function testInsertSameNameDifferentCategory()
    {
        $parameters = [
            "title" => "New Police Story",
            "category_id" => 1,
            "rate" => 12000,
            "quantity" => 8
        ];

        $this->post('/cd', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "cd" => ["id", "category_id", "category", "title", "rate", "quantity"]
        ]);
    }

    /**
     * Test PUT /cd/{id} endpoint
     * Case: Edit CD success
     */
    public function testEditCDSuccess()
    {
        $parameters = [
            "title" => "New Police Story 2",
            "category_id" => 1,
            "rate" => 12000,
            "quantity" => 10
        ];

        $this->put('/cd/4', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "cd" => ["id", "category_id", "category", "title", "rate", "quantity"]
        ]);
    }

    /**
     * Test PUT /cd/{id} endpoint
     * Case: Edit CD become duplicate
     */
    public function testEditCDBecomeDuplicate()
    {
        $parameters = [
            "title" => "Kimi No Na Wa",
            "category_id" => 1,
            "rate" => 12000,
            "quantity" => 10
        ];

        $this->put('/cd/4', $parameters);
        $this->seeStatusCode(409);
        $this->seeJsonStructure(["message"]);
    }

    /**
     * Test DELETE /cd/{id} endpoint
     * Case: Delete CD success
     */
    public function testDeleteCDSuccess()
    {
        $this->delete('/cd/4');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "cd" => ["id", "category_id", "category", "title", "rate", "quantity"]
        ]);
    }

    /**
     * Test GET /cd/{id} endpoint
     * Case: Success getting CD by ID
     */
    public function testGetCDByIDSuccess()
    {
        $this->get('/cd/1');
        $this->seeStatusCode(200);
        $this->seeJsonStructure(["id", "category_id", "category", "title", "rate", "quantity"]);
    }
}
