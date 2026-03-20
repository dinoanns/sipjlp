<?php

namespace App\Policies;

use App\Models\Pjlp;
use App\Models\User;

class PjlpPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['pjlp.view-self', 'pjlp.view-unit', 'pjlp.view-all']);
    }

    public function view(User $user, Pjlp $pjlp): bool
    {
        if ($user->can('pjlp.view-all')) {
            return true;
        }

        if ($user->can('pjlp.view-unit')) {
            if ($user->unit && $user->unit->value !== 'all') {
                return $pjlp->unit->value === $user->unit->value;
            }
            return true;
        }

        if ($user->can('pjlp.view-self')) {
            return $pjlp->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('pjlp.create');
    }

    public function update(User $user, Pjlp $pjlp): bool
    {
        return $user->can('pjlp.edit');
    }

    public function delete(User $user, Pjlp $pjlp): bool
    {
        return $user->can('pjlp.delete');
    }
}
