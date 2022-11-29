<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @test
     */
    public function authenticate()
    {
        $this->json('GET', '/invalid_credential')
            ->assertStatus(200)
            ->assertJson(['data' => 'invalid_credential']);
    }
}
