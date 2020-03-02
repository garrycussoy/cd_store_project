<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CategoryTest extends TestCase
{   
    /**
     * Test POST /category endpoint
     * Case: Success post new category
     */
    public function testPostCategorySuccess()
    {
        $this->artisan('migrate:refresh');
        $this->artisan('db:seed');
        $parameters = ["name" => "Comedy"];        

        $this->post('/category', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "category" => ["id", "name", "created_at"]
        ]);
    }

    /**
     * Test GET /category endpoint
     * Case: Success get all categories
     */
    public function testGetCategorySuccess()
    {
        $this->get('/category');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([["id", "name", "created_at", "updated_at", "deleted_at"]]);
    }

    /**
     * Test POST /category endpoint
     * Case: Duplicate entry
     */
    public function testPostCategoryDuplicateEntry()
    {
        $parameters = ["name" => "Comedy"];        

        $this->post('/category', $parameters);
        $this->seeStatusCode(409);
        $this->seeJsonStructure([
            "message"
        ]);
    }

    /**
     * Test PUT /category endpoint
     * Case: Success editting the category
     */
    public function testPutCategorySuccess()
    {
        $parameters = ["name" => "Fantasy"];        

        $this->put('/category/3', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "category" => [
                "id",
                "name",
                "created_at",
                "updated_at"
            ]
        ]);
    }

    /**
     * Test DELETE /category endpoint
     * Case: Success deleting the category
     */
    public function testDeleteCategorySuccess()
    {
        $this->delete('/category/3');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "category" => [
                "id",
                "name",
                "created_at",
                "deleted_at"
            ]
        ]);
    }
}
