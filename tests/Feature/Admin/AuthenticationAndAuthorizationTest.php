<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AuthenticationAndAuthorizationTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/airlines')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_logout(): void
    {
        $user = $this->admin();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_can_access_the_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Airport Ops');
    }

    public function test_non_admin_users_receive_a_forbidden_response(): void
    {
        $this->actingAs($this->agent())->get('/admin')->assertForbidden();
        $this->actingAs($this->manager())->get('/admin/airlines')->assertForbidden();
    }

    public function test_admin_can_access_every_module_one_index(): void
    {
        $admin = $this->admin();

        foreach (['airlines', 'airports', 'aircraft', 'terminals', 'gates', 'runways'] as $resource) {
            $this->actingAs($admin)->get("/admin/{$resource}")->assertOk();
        }
    }

    public function test_legacy_dashboard_route_redirects_authenticated_users_to_the_admin_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_a_user_must_have_a_known_role_to_pass_the_role_middleware(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_AGENT]);

        $this->actingAs($user)->get('/admin/airports')->assertForbidden();
    }
}
