<?php

namespace Rooles\Traits;

use Rooles\RoleRepo;
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
     * @return \Rooles\Role
     */
    public function getRoleAttribute($role)
    {

        /** @var RoleRepo $roleRepo */
        $roleRepo = App::make('Rooles\RoleRepo');

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
