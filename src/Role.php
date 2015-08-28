<?php

namespace Rooles;

use Rooles\Contracts\Permissions as PermissionsContract;
use Rooles\Contracts\Role as RoleContract;

/**
 * Class Role
 * @package Rooles
 */
class Role implements RoleContract
{

    /**
     * Role name
     *
     * @var string
     */
    protected $name;

    /**
     * @var PermissionsContract
     */
    protected $permissions;

    /**
     * Constructor
     *
     * @param string $name
     * @param PermissionsContract $permissions
     */
    public function __construct($name, PermissionsContract $permissions)
    {
        $this->name = $name;
        $this->permissions = $permissions;
    }

    /**
     * Return role name
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Return role id
     *
     * @return string
     */
    public function id()
    {
        return mb_strtolower($this->name());
    }

    /**
     * Grants a single or multiple (array) permission
     *
     * @param array|string $permissions
     *
     * @return Role
     */
    public function grant($permissions)
    {
        $this->permissions->set($permissions, '*');

        return $this;
    }

    /**
     * Denies a single or multiple (array) permission
     *
     * @param array|string $permissions
     *
     * @return Role
     */
    public function deny($permissions)
    {
        $this->permissions->set($permissions, '!');

        return $this;
    }

    /**
     * Check permission for a single or multiple permission query
     *
     * @param array|string $permissions
     *
     * @return bool
     */
    public function can($permissions)
    {
        return $this->permissions->evaluate($permissions);
    }

    /**
     * Invert the result of can
     *
     * @param array|string $permissions
     *
     * @return bool
     */
    public function cannot($permissions)
    {
        return !$this->can($permissions);
    }

    /**
     * Verify if the current role is the one provided
     *
     * @param string $roleName
     *
     * @return bool
     */
    public function is($roleName)
    {
        return $this->id() === mb_strtolower($roleName);
    }

    /**
     * Verify if the current role is in the provided array
     *
     * @param array $roles
     *
     * @return bool
     */
    public function isIn(array $roles)
    {
        return array_search($this->id(), array_map('mb_strtolower', $roles)) !== false;
    }

    /**
     * If the object is called as a string will return the role name
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->name();
    }

}
