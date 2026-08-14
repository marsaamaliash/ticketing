<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view-tickets')
            || $user->can('view-all-tickets')
            || $user->hasAnyRole(['cs', 'manager', 'teknisi', 'admin']);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('cs') || $user->hasRole('admin');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->hasRole('cs') || $user->hasRole('admin');
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasRole('admin');
    }
}
