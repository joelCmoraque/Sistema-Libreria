<?php

namespace Tests\Unit;

use Tests\TestCase;

class SalidasTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_redirection_route(): void
    {
        $response=$this->get('/deposite');
      //  $response->dump();
        $response->assertStatus(200);

    }
}
