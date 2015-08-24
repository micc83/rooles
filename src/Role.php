<?php

namespace Rooles;

use InvalidArgumentException;

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
     * Grants a single or multiple (array) permission
     *
     * @param array|string $permissions
     *
     * @return $this;
     */
    public function grant($permissions)
    {
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                $this->setGrant($permission);
            }
        } elseif (is_string($permissions)) {
            $this->setGrant($permissions);
        }

        return $this;
    }

    /**
     * Set a single grant
     *
     * @param $permission
     */
    protected function setGrant($permission)
    {
        $this->array_set($this->permissions, $permission, '*');
    }

    /**
     * Store the grants/deny in array
     *
     * @param $array
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    protected function array_set(&$array, $key, $value)
    {

        $key = $key . '.*';

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (isset( $array[$key] ) && $array[$key] === '*') {
                $array[$key] = ['*' => '*'];
            } elseif ( ! isset( $array[$key] ) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
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
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                $this->setDeny($permission);
            }
        } elseif (is_string($permissions)) {
            $this->setDeny($permissions);
        }

        return $this;
    }

    /**
     * Set a single deny
     *
     * @param $permission
     */
    protected function setDeny($permission)
    {
        $this->array_set($this->permissions, $permission, '!');
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
        return ! $this->can($permissions);
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
            if (strpos($permissions, '&') !== false) {
                $permissions = explode('&', $permissions);
            } else {
                return $this->checkPermissions($permissions, $this->permissions);
            }
        }

        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if ( ! $this->checkPermissions($permission)) {
                    return false;
                }
            }

            return true;
        }

        throw new InvalidArgumentException('Permissions can only be of type array or string');
    }


    /**
     * Check permissions using OR strategy with the "|" operator
     *
     * @param $permissions
     *
     * @return bool
     */
    protected function checkPermissions($permissions)
    {

        foreach (explode('|', $permissions) as $permission) {
            if ($this->checkPermission($permission)) {
                return true;
            }
        }

        return false;
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

        $permission = rtrim($permission, '.*') . '.*';

        $permsLevel = $this->permissions;
        foreach (explode('.', $permission) as $part) {
            if (isset( $permsLevel[$part] )) {
                $permsLevel = $permsLevel[$part];
            } elseif (isset( $permsLevel['*'] )) {
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
     * Verify if the current role is the one provided
     *
     * @param string $roleName
     *
     * @return bool
     */
    public function is($roleName)
    {
        return $this->name === $roleName;
    }

    /**
     * Verify if the current role is in the provided array
     *
     * @param array $roles
     *
     * @return mixed
     */
    public function isIn(array $roles)
    {
        return array_search($this->name, $roles) !== false;
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
     * If the object is called as a string will return the role name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

}
