<?php

namespace Rooles;

use InvalidArgumentException;
use Rooles\Contracts\RoleRepository;
use Rooles\Contracts\Role as RoleContract;
use UnexpectedValueException;

/**
 * Class RoleManager
 * @package Rooles
 */
class RoleManager implements RoleRepository
{

    /**
     * Roles storage array
     *
     * @var array
     */
    protected $roles = [];

    /**
     * Get an existing role or create a new one with the given name
     *
     * @param string $roleName
     *
     * @return RoleContract
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
     * Get the role with the given name
     *
     * Return a "default" role if the given name is empty or
     * throw InvalidArgumentException if role name is not found
     *
     * @param string $roleName
     *
     * @return RoleContract
     */
    public function get($roleName)
    {

        if (empty( $roleName )) {
            return $this->getOrCreate('default');
        }

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
     * @return RoleContract
     */
    public function create($roleName)
    {
        $role = new Role($roleName);
        $this->add($role);

        return $role;
    }

    /**
     * Add an existing role object to the repository
     *
     * @param RoleContract $role
     */
    public function add(RoleContract $role)
    {
        if (isset( $this->roles[$role->name()] )) {
            throw new UnexpectedValueException('Duplicated role!');
        }
        $this->roles[$role->name()] = $role;
    }

}
