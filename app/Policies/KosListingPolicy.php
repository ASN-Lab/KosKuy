<?php

namespace App\Policies;

use App\Models\KosListing;
use App\Models\User;

class KosListingPolicy
{
    public function viewAny(?User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function view(?User $user, KosListing $kosListing): bool
    {
        return $user->role === 'admin' || $user->id === $kosListing->owner_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function update(User $user, KosListing $kosListing): bool
    {
        return $user->role === 'admin' || $user->id === $kosListing->owner_id;
    }

    public function delete(User $user, KosListing $kosListing): bool
    {
        return $user->role === 'admin' || $user->id === $kosListing->owner_id;
    }
}
