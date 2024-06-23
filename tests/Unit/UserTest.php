<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Facade;

class UserTest extends TestCase
{
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

}
