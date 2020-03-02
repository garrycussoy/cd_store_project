<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RentTest extends TestCase
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
}
