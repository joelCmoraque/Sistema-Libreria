<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Facade;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $app = require __DIR__.'/../../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        Facade::setFacadeApplication($app);
    }
   
   public function it_casts_email_verified_at_to_datetime()
   {
       $user = User::create([
           'name' => 'usuarioPrueba',
           'email' => 'prueba@example.com',
           'password' => Hash::make('secr$%24'), 
           'email_verified_at' => '2024-12-07 10:00:00',
       ]);

       $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
   }

}
