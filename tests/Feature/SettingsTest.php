<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Covers the account settings area available to every signed-in role.
 */
class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_away_from_settings(): void
    {
        $this->get('/pengaturan')->assertRedirect('/login');
    }

    public function test_any_role_can_reach_settings(): void
    {
        foreach (['student', 'owner', 'admin'] as $role) {
            $this->actingAs(User::factory()->{$role}()->create())
                ->get('/pengaturan')
                ->assertOk()
                ->assertSee('Pengaturan akun', false);
        }
    }

    public function test_profile_is_updated(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user)
            ->put('/pengaturan/profil', ['name' => 'Rina Melati', 'email' => $user->email, 'phone' => '081299988877'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Rina Melati', 'phone' => '081299988877']);
    }

    public function test_password_is_changed(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user)
            ->put('/pengaturan/sandi', [
                'current_password' => 'password',
                'password' => 'rahasia-baru',
                'password_confirmation' => 'rahasia-baru',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('rahasia-baru', $user->fresh()->password));
    }

    public function test_password_change_rejects_a_wrong_current_password(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user)
            ->put('/pengaturan/sandi', [
                'current_password' => 'salah-sandi',
                'password' => 'rahasia-baru',
                'password_confirmation' => 'rahasia-baru',
            ])
            ->assertSessionHasErrors('current_password');
    }

    public function test_notification_preferences_are_stored(): void
    {
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->put('/pengaturan/notifikasi', ['notify_email' => '1'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'notify_email' => 1, 'notify_whatsapp' => 0]);
    }

    public function test_avatar_can_be_uploaded_and_removed(): void
    {
        Storage::fake('public');

        $user = User::factory()->student()->create();

        $this->actingAs($user)->get('/pengaturan');

        $this->post('/pengaturan/foto', ['avatar' => UploadedFile::fake()->image('avatar.jpg')])
            ->assertRedirect('/pengaturan');

        $this->assertNotNull($user->fresh()->avatar_path);

        $this->delete('/pengaturan/foto')
            ->assertRedirect('/pengaturan');

        $this->assertNull($user->fresh()->avatar_path);
    }

    public function test_account_can_be_deleted(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user)
            ->delete('/pengaturan/akun', ['password' => 'password'])
            ->assertRedirect('/');

        $this->assertNull(User::find($user->id));
        $this->assertGuest();
    }
}