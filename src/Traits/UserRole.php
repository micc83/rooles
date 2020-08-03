<?php

namespace Rooles\Traits;

use Rooles\Contracts\Role;
use Rooles\Contracts\RoleRepository;

/**
 * Class UserRole
 * @package Rooles\Traits
 *
 * @property Role|string $role
 */
trait UserRole
{
    /**
     * Called on boot of the model
     *
     * @param string $role
     *
     * @return Role
     */
    public function getRoleAttribute($role)
    {
        return app(RoleRepository::class)->get($role);
    }

    /**
     * @param array|string $permissions
     *
     * @return bool
     */
    public function can($permissions)
    {
        return $this->role->can($permissions);
    }

    /**
     * @param array|string $permissions
     *
     * @return bool
     */
    public function cannot($permissions)
    {
        return $this->role->cannot($permissions);
    }
}
