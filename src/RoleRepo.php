<?php

namespace Rooles;

use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class RoleRepo
 * @package App\Crm\Permission
 */
class RoleRepo
{

    /**
     * Roles storage array
     *
     * @var array
     */
    protected $roles = [];

    /**
     * @param string $roleName
     *
     * @return Role
     */
    public function getOrCreate($roleName)
    {
        try {
            return $this->get($roleName);
        } catch (\Exception $e) {
            return $this->create($roleName);
        }
    }

    /**
     * @param string $roleName
     *
     * @return Role
     */
    public function get($roleName)
    {

        if (isset( $this->roles[$roleName] )) {
            return $this->roles[$roleName];
        }

        throw new InvalidArgumentException('Role not found');

    }

    /**
     * Create a new role
     *
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
     * Add an existing role
     *
     * @param Role $role
     */
    public function add(Role $role)
    {
        if (isset( $this->roles[$role->name()] )) {
            throw new UnexpectedValueException('Duplicated role!');
        }
        $this->roles[$role->name()] = $role;
    }

}
