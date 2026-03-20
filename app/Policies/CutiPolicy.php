<?php

namespace App\Policies;

use App\Models\Cuti;
use App\Models\User;

class CutiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['cuti.view-self', 'cuti.view-unit', 'cuti.view-all']);
    }

    public function view(User $user, Cuti $cuti): bool
    {
        if ($user->can('cuti.view-all')) {
            return true;
        }

        if ($user->can('cuti.view-unit')) {
            $pjlp = $cuti->pjlp;
            if ($user->unit && $user->unit->value !== 'all') {
                return $pjlp->unit->value === $user->unit->value;
            }
            return true;
        }

        if ($user->can('cuti.view-self')) {
            return $cuti->pjlp_id === $user->pjlp?->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('cuti.create') && $user->pjlp !== null;
    }

    public function approve(User $user, Cuti $cuti): bool
    {
        if (!$user->can('cuti.approve')) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('koordinator')) {
            $pjlp = $cuti->pjlp;
            if ($user->unit && $user->unit->value !== 'all') {
                return $pjlp->unit->value === $user->unit->value;
            }
            return true;
        }

        return false;
    }
}
