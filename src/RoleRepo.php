<?php

namespace Roole;

use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class RoleRepo
 * @package App\Crm\Permission
 */
class RoleRepo
{

    /**
     * @var array
     */
    protected $roles = [];

    /**
     * @param string $roleName
     *
     * @return Role
     */
    public function create($roleName)
    {
        $role = new Role($roleName);
        $this->add($role);

        return $role;
    }

    /**
     * @param Role $role
     */
    public function add(Role $role)
    {
        if (array_search($role, $this->roles) !== false) {
            throw new UnexpectedValueException('Duplicated role!');
        }
        $this->roles[(string) $role] = $role;
    }

    /**
     * @param string $role
     *
     * @return Role
     */
    public function get($role)
    {

        if (isset( $this->roles[$role] )) {
            return $this->roles[$role];
        }

        throw new InvalidArgumentException('Role not found');

    }

}
