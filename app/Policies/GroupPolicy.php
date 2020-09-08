<?php

namespace App\Policies;

use App\Role;
use App\User;
use App\Group;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    // Super admin access
    public function before($user)
    {
        $administrator = Role::where('name', 'Administrator')->first();

        return $user->roles->contains($administrator->id);
    }

    public function view(User $user, Group $group)
    {
        return $user->id === $group->user_id;
    }

    public function create(User $user)
    {
        // potential for subscription status check here
    }

    public function update(User $user, Group $group)
    {
        return $user->id === $group->user_id;
    }

    public function delete(User $user, Group $group)
    {
        return $user->id === $group->user_id;
    }

}
