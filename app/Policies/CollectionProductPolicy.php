<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CollectionProduct;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollectionProductPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CollectionProduct');
    }

    public function view(AuthUser $authUser, CollectionProduct $collectionProduct): bool
    {
        return $authUser->can('View:CollectionProduct');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CollectionProduct');
    }

    public function update(AuthUser $authUser, CollectionProduct $collectionProduct): bool
    {
        return $authUser->can('Update:CollectionProduct');
    }

    public function delete(AuthUser $authUser, CollectionProduct $collectionProduct): bool
    {
        return $authUser->can('Delete:CollectionProduct');
    }

    public function restore(AuthUser $authUser, CollectionProduct $collectionProduct): bool
    {
        return $authUser->can('Restore:CollectionProduct');
    }

    public function forceDelete(AuthUser $authUser, CollectionProduct $collectionProduct): bool
    {
        return $authUser->can('ForceDelete:CollectionProduct');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CollectionProduct');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CollectionProduct');
    }

    public function replicate(AuthUser $authUser, CollectionProduct $collectionProduct): bool
    {
        return $authUser->can('Replicate:CollectionProduct');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CollectionProduct');
    }

}