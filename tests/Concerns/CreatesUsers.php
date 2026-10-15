<?php

namespace Tests\Concerns;

use App\Models\User;

trait CreatesUsers
{
    protected function admin(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => User::ROLE_ADMIN], $attributes));
    }

    protected function manager(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => User::ROLE_MANAGER], $attributes));
    }

    protected function agent(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => User::ROLE_AGENT], $attributes));
    }
}
