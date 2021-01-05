<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void 
     */
    public function testHome()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }

     /**
     * A basic test example.
     *
     * @return void
     */
    
    // public function testLoginRoute()
    // {
    //     $response = $this->get('/login');

    //     $response->assertStatus(200);
    // }

     /**
     * A basic test example.
     *
     * @return void
     */
    public function test404Page()
    {
        $response = $this->get('/error404page');

        $response->assertStatus(404);
    }
}
