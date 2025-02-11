<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    /** @test */
    public function it_can_check_if_a_user_is_admin()
    {
        $user = new User(['is_admin' => true]);

        $this->assertTrue($user->is_admin);
    }
}
