<?php

namespace Rooles;

use App\User;
use Rooles\Traits\UserRole;

/**
 * Class RoleMiddleware
 * @package Rooles
 */
class RoleMiddleware extends BaseMiddleware
{
    /**
     * @param string $roles
     * @param User|UserRole $user
     *
     * @return bool
     */
    protected function verifyCondition($roles, $user)
    {
        return !$user->role->isIn(explode('|', $roles));
    }

}
