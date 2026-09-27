<?php

namespace App\Policies;

use App\Models\ThriftProduct;
use App\Models\User;

class ThriftProductPolicy
{
    public function viewAny(?User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function view(?User $user, ThriftProduct $product): bool
    {
        return $user->role === 'admin' || $user->id === $product->seller_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function update(User $user, ThriftProduct $product): bool
    {
        return $user->role === 'admin' || $user->id === $product->seller_id;
    }

    public function delete(User $user, ThriftProduct $product): bool
    {
        return $user->role === 'admin' || $user->id === $product->seller_id;
    }
}
