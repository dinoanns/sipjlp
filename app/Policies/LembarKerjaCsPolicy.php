<?php

namespace App\Policies;

use App\Models\LembarKerjaCs;
use App\Models\User;

class LembarKerjaCsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny([
            'lembar-kerja-cs.view-self',
            'lembar-kerja-cs.view-unit',
            'lembar-kerja-cs.view-all',
        ]);
    }

    public function view(User $user, LembarKerjaCs $lembarKerjaCs): bool
    {
        if ($user->can('lembar-kerja-cs.view-all')) {
            return true;
        }

        if ($user->can('lembar-kerja-cs.view-unit')) {
            $pjlp = $lembarKerjaCs->pjlp;
            if ($user->unit && $user->unit->value !== 'all') {
                return $pjlp->unit->value === $user->unit->value;
            }
            return true;
        }

        if ($user->can('lembar-kerja-cs.view-self')) {
            return $lembarKerjaCs->pjlp_id === $user->pjlp?->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('lembar-kerja-cs.create');
    }

    public function update(User $user, LembarKerjaCs $lembarKerjaCs): bool
    {
        if (!$lembarKerjaCs->canEdit()) {
            return false;
        }

        if ($user->can('lembar-kerja-cs.view-all')) {
            return true;
        }

        if ($user->can('lembar-kerja-cs.view-unit')) {
            $pjlp = $lembarKerjaCs->pjlp;
            if ($user->unit && $user->unit->value !== 'all') {
                return $pjlp->unit->value === $user->unit->value;
            }
            return true;
        }

        if ($user->can('lembar-kerja-cs.view-self')) {
            return $lembarKerjaCs->pjlp_id === $user->pjlp?->id;
        }

        return false;
    }

    public function delete(User $user, LembarKerjaCs $lembarKerjaCs): bool
    {
        if (!$lembarKerjaCs->isDraft()) {
            return false;
        }

        if ($user->can('lembar-kerja-cs.view-all')) {
            return true;
        }

        if ($user->can('lembar-kerja-cs.view-self')) {
            return $lembarKerjaCs->pjlp_id === $user->pjlp?->id;
        }

        return false;
    }

    public function validate(User $user, LembarKerjaCs $lembarKerjaCs): bool
    {
        if (!$user->can('lembar-kerja-cs.validate')) {
            return false;
        }

        if ($user->can('lembar-kerja-cs.view-all')) {
            return true;
        }

        if ($user->can('lembar-kerja-cs.view-unit')) {
            $pjlp = $lembarKerjaCs->pjlp;
            if ($user->unit && $user->unit->value !== 'all') {
                return $pjlp->unit->value === $user->unit->value;
            }
            return true;
        }

        return false;
    }
}
