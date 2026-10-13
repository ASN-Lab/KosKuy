<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['admin', 'owner']);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'avatar_path',
        'notify_email',
        'notify_whatsapp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notify_email' => 'boolean',
            'notify_whatsapp' => 'boolean',
        ];
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? asset('storage/' . $this->avatar_path) : null;
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'owner' => 'Pemilik / Pengelola Kos',
            default => 'Mahasiswa / Pencari Kos',
        };
    }

    public function kosListings(): HasMany
    {
        return $this->hasMany(KosListing::class, 'owner_id');
    }

    public function thriftProducts(): HasMany
    {
        return $this->hasMany(ThriftProduct::class, 'seller_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function thriftPurchases(): HasMany
    {
        return $this->hasMany(ThriftPurchase::class, 'buyer_id');
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }
}
