<?php

namespace Rooles;

use Rooles\Contracts\Permissions as PermissionsContract;

/**
 * Class Role
 * @package Rooles
 */
class Role implements Contracts\Role
{

    /**
     * Role name
     *
     * @var string
     */
    protected $name;

    /**
     * @var Permissions
     */
    protected $permissions;

    /**
     * Constructor
     *
     * @param string $name
     * @param PermissionsContract|null $permissions
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
     * Grants a single or multiple (array) permission
     *
     * @param array|string $permissions
     *
     * @return $this;
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
     * @return $this;
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
        return $this->permissions->check($permissions);
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
        return $this->name() === $roleName;
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
        return array_search($this->name(), $roles) !== false;
    }

    /**
     * If the object is called as a string will return the role name
     *
     * @return string
     */
    public final function __toString()
    {
        return $this->name();
    }

}
