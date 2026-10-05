<?php

namespace Tests\Feature;

use App\Models\KosListing;
use App\Models\ThriftProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Covers the session flows that sit in front of the dashboard: registration,
 * login/logout, route protection, and the newsletter form.
 *
 * NB: `VerifyCsrfToken` short-circuits while the test runner is active, so CSRF
 * itself is exercised by storage/app/smoke-routes.php instead, which dispatches
 * through the real middleware stack.
 */
class AuthAndNewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_away_from_the_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_user_can_register_and_reach_the_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'phone' => '081234567890',
            'role' => 'student',
            'password' => 'rahasia-kuat',
            'password_confirmation' => 'rahasia-kuat',
            'terms' => '1',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'budi@example.test']);

        $this->assertAuthenticated();
        $this->get('/dashboard')->assertOk();
    }

    public function test_registration_hashes_the_password(): void
    {
        $this->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.test',
            'phone' => '081234567891',
            'role' => 'student',
            'password' => 'rahasia-kuat',
            'password_confirmation' => 'rahasia-kuat',
            'terms' => '1',
        ]);

        $user = User::where('email', 'siti@example.test')->firstOrFail();

        $this->assertNotSame('rahasia-kuat', $user->password);
        $this->assertTrue(Hash::check('rahasia-kuat', $user->password));
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $this->post('/register', [
            'name' => 'Rina',
            'email' => 'rina@example.test',
            'phone' => '081234567892',
            'role' => 'student',
            'password' => 'rahasia-kuat',
            'password_confirmation' => 'berbeda-sekali',
            'terms' => '1',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'rina@example.test']);
    }

    public function test_registration_requires_accepting_the_terms(): void
    {
        $this->post('/register', [
            'name' => 'Tono',
            'email' => 'tono@example.test',
            'phone' => '081234567893',
            'role' => 'student',
            'password' => 'rahasia-kuat',
            'password_confirmation' => 'rahasia-kuat',
        ])->assertSessionHasErrors('terms');

        $this->assertDatabaseMissing('users', ['email' => 'tono@example.test']);
    }

    public function test_registration_rejects_a_duplicate_email(): void
    {
        User::factory()->create(['email' => 'kembar@example.test']);

        $this->post('/register', [
            'name' => 'Kembar',
            'email' => 'kembar@example.test',
            'phone' => '081234567894',
            'role' => 'student',
            'password' => 'rahasia-kuat',
            'password_confirmation' => 'rahasia-kuat',
            'terms' => '1',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, User::where('email', 'kembar@example.test')->count());
    }

    public function test_user_can_log_in_with_the_right_password(): void
    {
        $user = User::factory()->create([
            'email' => 'jaya@example.test',
            'password' => Hash::make('rahasia-kuat'),
        ]);

        $this->post('/login', [
            'email' => 'jaya@example.test',
            'password' => 'rahasia-kuat',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_the_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'salah@example.test',
            'password' => Hash::make('rahasia-kuat'),
        ]);

        $this->post('/login', [
            'email' => 'salah@example.test',
            'password' => 'kata-sandi-salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_users_are_redirected_away_from_the_guest_pages(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/login')->assertRedirect('/dashboard');
        $this->get('/register')->assertRedirect('/dashboard');
    }

    public function test_user_can_log_out_and_lose_dashboard_access(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/logout')->assertRedirect('/');

        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_only_shows_the_signed_in_users_own_listings(): void
    {
        $user = User::factory()->create();
        $stranger = User::factory()->create();

        $mine = KosListing::factory()->for($user, 'owner')->create(['title' => 'Kos Milik Saya']);
        KosListing::factory()->for($stranger, 'owner')->create(['title' => 'Kos Milik Orang Lain']);

        ThriftProduct::factory()->for($user, 'seller')->create(['title' => 'Thrift Milik Saya']);
        ThriftProduct::factory()->for($stranger, 'seller')->create(['title' => 'Thrift Milik Orang Lain']);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee($mine->title, false)
            ->assertSee('Thrift Milik Saya', false)
            ->assertDontSee('Kos Milik Orang Lain', false)
            ->assertDontSee('Thrift Milik Orang Lain', false);
    }

    public function test_newsletter_accepts_a_valid_address_and_ignores_duplicates(): void
    {
        $this->post('/newsletter', ['email' => 'pendatang@example.test'])
            ->assertRedirect();

        $this->assertContains('pendatang@example.test', Cache::get('newsletter:subscribers', []));

        $this->post('/newsletter', ['email' => 'pendatang@example.test'])->assertRedirect();

        $this->assertCount(1, Cache::get('newsletter:subscribers', []));
    }

    public function test_newsletter_rejects_an_invalid_address(): void
    {
        $this->post('/newsletter', ['email' => 'bukan-email'])
            ->assertSessionHasErrors('email');

        $this->assertEmpty(Cache::get('newsletter:subscribers', []));
    }

    public function test_newsletter_answers_json_callers_with_json(): void
    {
        $this->postJson('/newsletter', ['email' => 'api@example.test'])
            ->assertOk()
            ->assertJsonPath('message', 'Berhasil! Cek inbox kamu untuk konfirmasi.');
    }
}