<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_categories_index(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/categories')
            ->assertSuccessful();
    }

    public function test_admin_can_view_kos_listings_index(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/kos-listings')
            ->assertSuccessful();
    }

    public function test_thrift_products_are_not_exposed_in_the_admin_panel(): void
    {
        // Thrift is a standalone business line with its own future flow; its
        // Filament resource was intentionally removed from the admin panel.
        $this->actingAs($this->admin())
            ->get('/admin/thrift-products')
            ->assertNotFound();
    }

    public function test_admin_can_view_users_index(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/users')
            ->assertSuccessful();
    }

    public function test_admin_can_view_create_user_page(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/users/create')
            ->assertSuccessful();
    }

    public function test_owner_reaches_the_panel_but_cannot_manage_users_or_categories(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)->get('/admin')->assertSuccessful();
        $this->actingAs($owner)->get('/admin/users')->assertForbidden();
        $this->actingAs($owner)->get('/admin/categories')->assertForbidden();
    }
}
