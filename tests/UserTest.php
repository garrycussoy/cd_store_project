<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserTest extends TestCase
{   
    /**
     * Test GET /user endpoint
     * Case: Success get all users without filter
     */
    public function testGetAllUsersWithoutFilter()
    {
        $this->artisan('migrate:refresh');
        $this->artisan('db:seed');

        $this->get('/user');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([["id", "name", "identity_type", "identity_number", "phone_number", "address"]]);
    }

    /**
     * Test GET /user endpoint
     * Case: Success get all users with filter
     */
    public function testGetAllUsersWithFilter()
    {
        $parameters = [
            "name" => "Gar",
            "identity_type" => "KTP",
            "identity_number" => "31",
            "phone_number" => "08",
            "address" => "Jak"
        ];

        $this->get('/user', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([["id", "name", "identity_type", "identity_number", "phone_number", "address"]]);
    }

    /**
     * Test POST /user endpoint
     * Case: Success add new user
     */
    public function testAddNewUserSuccess()
    {
        $parameters = [
            "name" => "Gar",
            "identity_type" => "KTP",
            "identity_number" => "31",
            "phone_number" => "08",
            "address" => "Jak"
        ];

        $this->post('/user', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "user" => ["id", "name", "identity_type", "identity_number", "phone_number", "address"]
        ]);
    }

    /**
     * Test POST /user endpoint
     * Case: Duplicate user by identity
     */
    public function testDuplicateUserByIdentity()
    {
        $parameters = [
            "name" => "Gar",
            "identity_type" => "KTP",
            "identity_number" => "31",
            "phone_number" => "09",
            "address" => "Jak"
        ];

        $this->post('/user', $parameters);
        $this->seeStatusCode(409);
        $this->seeJsonStructure(["message"]);
    }

    /**
     * Test POST /user endpoint
     * Case: Duplicate user by phone number
     */
    public function testDuplicateUserByPhone()
    {
        $parameters = [
            "name" => "Gar",
            "identity_type" => "KTP",
            "identity_number" => "32",
            "phone_number" => "08",
            "address" => "Jak"
        ];

        $this->post('/user', $parameters);
        $this->seeStatusCode(409);
        $this->seeJsonStructure(["message"]);
    }

    /**
     * Test PUT /user/{id} endpoint
     * Case: Success edit a user
     */
    public function testEditUserSuccess()
    {
        $parameters = [
            "name" => "Garry",
            "identity_type" => "KTP",
            "identity_number" => "312",
            "phone_number" => "087",
            "address" => "Jakarta"
        ];

        $this->put('/user/1', $parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "user" => ["id", "name", "identity_type", "identity_number", "phone_number", "address"]
        ]);
    }

    /**
     * Test DELETE /user{id} endpoint
     * Case: Success delete a user
     */
    public function testDeleteUserSuccess()
    {
        $this->delete('/user/1');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            "message",
            "user" => ["id", "name", "identity_type", "identity_number", "phone_number", "address"]
        ]);
    }

    /**
     * Test GET /user/{id} endpoint
     * Case: Success delete a user
     */
    public function testGetUserByIDSuccess()
    {
        $this->get('/user/2');
        $this->seeStatusCode(200);
        $this->seeJsonStructure(["id", "name", "identity_type", "identity_number", "phone_number", "address"]);
    }
}
