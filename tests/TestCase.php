<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected User $user;

    public function actingAsUser()
    {
        $this->user = User::factory()->create();

        $this->actingAs($this->user, 'api');
        return $this->user;
    }
}
