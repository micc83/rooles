<?php

namespace Rooles;

use Closure;

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
     * Permissions storage
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
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
        return $this->each($permissions, function ($permission) {
            $this->setPermission($permission, '*');
        });
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
        return $this->each($permissions, function ($permission) {
            $this->setPermission($permission, '!');
        });
    }

    /**
     * Check permission for a single or multiple (array or "&" operator) permission query
     *
     * @param array|string $permissions
     *
     * @return bool
     */
    public function can($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        return $this->checkPermissions($permissions);
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
     * Store the permissions in array
     *
     * @param string $permission
     * @param string $value
     *
     * @return void
     */
    protected function setPermission($permission, $value)
    {
        $permsLevel = &$this->permissions;

        $keys = $this->explodePermission($permission);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (isset($permsLevel[$key]) && $permsLevel[$key] === '*') {
                $permsLevel[$key] = ['*' => '*'];
            } elseif (!isset($permsLevel[$key]) || !is_array($permsLevel[$key])) {
                $permsLevel[$key] = [];
            }

            $permsLevel = &$permsLevel[$key];
        }

        $permsLevel[array_shift($keys)] = $value;
    }

    /**
     * Check permissions using OR strategy with the "|" operator
     *
     * @param array $permissionsArray
     *
     * @return bool
     */
    protected function checkPermissions(array $permissionsArray)
    {
        $result = false;

        foreach ($permissionsArray as $permissions) {
            $result = false;
            foreach (explode('&', $permissions) as $permissionGroup) {
                $result = false;
                foreach (explode('|', $permissionGroup) as $permission) {
                    if ($this->checkPermission($permission)) {
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Check the value of a single permission
     *
     * @param string $permission
     *
     * @return bool
     */
    protected function checkPermission($permission)
    {
        $keys = $this->explodePermission($permission);

        $permsLevel = $this->permissions;
        foreach ($keys as $part) {
            if (isset($permsLevel[$part])) {
                $permsLevel = $permsLevel[$part];
            } elseif (isset($permsLevel['*'])) {
                $permsLevel = $permsLevel['*'];
            }
            if ($permsLevel === '*') {
                return true;
            } elseif ($permsLevel === '!') {
                return false;
            }
        }

        return false;
    }

    /**
     * Explode the permission string adding the wildcard at the end of the array
     *
     * @param string $permission
     * @return array
     */
    protected function explodePermission($permission)
    {
        return array_merge(explode('.', $this->removeEndingWildcard($permission)), ['*']);
    }

    /**
     * Remove the wildcard at the end of the string
     *
     * @param $key
     *
     * @return string
     */
    protected function removeEndingWildcard($key)
    {
        return preg_replace('/\.\*$/', '', $key);
    }

    /**
     * Loop over the array or pass directly the string to the provided closure
     *
     * @param array|string $permissions
     * @param callable $do
     * @return $this
     */
    protected function each($permissions, Closure $do)
    {
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                $do($permission);
            }
        } elseif (is_string($permissions)) {
            $do($permissions);
        }

        return $this;
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
