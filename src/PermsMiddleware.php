<?php

namespace Rooles;

use App\User;
use Rooles\Traits\UserRole;

/**
 * Class PermsMiddleware
 * @package Rooles
 */
class PermsMiddleware extends BaseMiddleware
{

    /**
     * @param string $permissions
     * @param User|UserRole $user
     * @return bool
     */
    protected function verifyCondition($permissions, $user)
    {
        return $user->role->cannot($permissions);
    }

}
