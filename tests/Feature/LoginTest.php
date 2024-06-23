<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use DatabaseTransactions;

    /** @test */
    public function users_can_login_and_access_admin()
    {
        $user = User::factory()->make([
            'email' => 'jcm@test.com',
            'password' => bcrypt('1234'), // Replace with actual password
        ]);
    
        $this->actingAs($user);
    
        $response = $this->get('/admin');
       // $response->dump();
        $response->assertStatus(200);
    }
}
