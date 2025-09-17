<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LoyaltyTransaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoyaltyTransactionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LoyaltyTransaction');
    }

    public function view(AuthUser $authUser, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $authUser->can('View:LoyaltyTransaction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LoyaltyTransaction');
    }

    public function update(AuthUser $authUser, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $authUser->can('Update:LoyaltyTransaction');
    }

    public function delete(AuthUser $authUser, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $authUser->can('Delete:LoyaltyTransaction');
    }

    public function restore(AuthUser $authUser, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $authUser->can('Restore:LoyaltyTransaction');
    }

    public function forceDelete(AuthUser $authUser, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $authUser->can('ForceDelete:LoyaltyTransaction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LoyaltyTransaction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LoyaltyTransaction');
    }

    public function replicate(AuthUser $authUser, LoyaltyTransaction $loyaltyTransaction): bool
    {
        return $authUser->can('Replicate:LoyaltyTransaction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LoyaltyTransaction');
    }

}