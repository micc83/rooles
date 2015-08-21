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
     * Create a new role
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function create ($roleName)
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
    public function add (Role $role)
    {
        if (isset($this->roles[(string)$role])) {
            throw new UnexpectedValueException('Duplicated role!');
        }
        $this->roles[(string)$role] = $role;
    }

    /**
     * @param string $role
     *
     * @return Role
     */
    public function get ($role)
    {

        if (isset($this->roles[$role])) {
            return $this->roles[$role];
        }

        throw new InvalidArgumentException('Role not found');

    }

    /**
     * @param string $role
     *
     * @return Role
     */
    public function getOrCreate ($role)
    {
        try {
            return $this->get($role);
        } catch (\Exception $e) {
            return $this->create($role);
        }
    }

}
