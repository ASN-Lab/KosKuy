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

    public function test_admin_can_view_thrift_products_index(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/thrift-products')
            ->assertSuccessful();
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
}
