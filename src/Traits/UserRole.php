<?php

namespace Roole\Traits;

use Roole\RoleRepo;
use Illuminate\Support\Facades\App;

/**
 * Class Role
 * @package App\Crm\Permission\Traits
 */
trait UserRole
{

    /**
     * Called on boot of the model
     *
     * @param string $role
     *
     * @return \Roole\Role
     */
    public function getRoleAttribute($role)
    {

        /** @var RoleRepo $roleRepo */
        $roleRepo = App::make('Roole\RoleRepo');

        return $roleRepo->get($role);

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
