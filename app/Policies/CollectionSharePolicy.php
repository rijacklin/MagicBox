<?php

namespace App\Policies;

use App\Models\CollectionShare;
use App\Models\User;

class CollectionSharePolicy
{
    /**
     * Determine whether the user can accept the share.
     */
    public function accept(User $user, CollectionShare $share): bool
    {
        return $share->user_id === $user->id && !$share->isAccepted();
    }

    /**
     * Determine whether the user can reject the share.
     */
    public function reject(User $user, CollectionShare $share): bool
    {
        return $share->user_id === $user->id && !$share->isAccepted();
    }

    /**
     * Determine whether the user can delete the share.
     */
    public function delete(User $user, CollectionShare $share): bool
    {
        return $share->collection->isOwnedBy($user);
    }
}
