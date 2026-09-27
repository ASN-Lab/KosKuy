<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserPasswordEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_user_with_blank_password_keeps_old_password(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@x.id',
            'password' => 'admin1234',
            'role' => 'admin',
        ]);

        $owner = User::create([
            'name' => 'Owner Baru',
            'email' => 'owner@x.id',
            'password' => 'owner1234',
            'phone' => '081234567890',
            'role' => 'owner',
        ]);

        $this->actingAs($admin);

        // Test via Filament EditUser Livewire component
        Livewire::test(\App\Filament\Resources\Users\Pages\EditUser::class, ['record' => $owner->id])
            ->fillForm([
                'name' => 'Owner Baru',
                'email' => 'owner@x.id',
                'phone' => '081234567890',
                'role' => 'owner',
                'password' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $owner->refresh();
        $this->assertTrue(Hash::check('owner1234', $owner->password), 'Password harus tetap valid setelah edit blank');
    }

    public function test_create_user_hashes_password(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@x.id',
            'password' => 'admin1234',
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Filament\Resources\Users\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'Owner Kedua',
                'email' => 'owner2@x.id',
                'phone' => '081234567891',
                'role' => 'owner',
                'password' => 'owner1234',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $owner = User::where('email', 'owner2@x.id')->first();
        $this->assertNotNull($owner);
        $this->assertTrue(Hash::check('owner1234', $owner->password), 'Password owner harus cocok setelah create');
    }
}
