<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserPasswordHAttributeTest extends TestCase
{
    public function testItHashesAUsersPasswordOnSet()
    {
        $password = 'unHashedPassword123';

        $user = User::factory()->make([
            'password' => $password,
        ]);

        $this->assertTrue(Hash::check($password, $user->password));
    }
}
