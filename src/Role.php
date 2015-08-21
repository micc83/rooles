<?php

namespace Roole;

use InvalidArgumentException;

/**
 * Class Role
 * @package App\Crm\Permission
 */
class Role
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $permissions = [];

    /**
     * @param string $name
     */
    public function __construct ($name)
    {
        $this->name = $name;
    }

    /**
     * @param array|string $permissions
     *
     * @return $this;
     */
    public function grant ($permissions)
    {
        if (is_array($permissions)) {

            foreach ($permissions as $permission) {
                $this->addSingleGrant($permission);
            }

        } elseif (is_string($permissions)) {
            $this->addSingleGrant($permissions);
        }

        return $this;
    }

    /**
     * @param $permission
     */
    protected function addSingleGrant ($permission)
    {
        $this->array_set($this->permissions, $permission, '*');
    }

    /**
     * @param $array
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function array_set (&$array, $key, $value)
    {

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * @param array|string $permissions
     *
     * @return bool
     */
    public function cannot ($permissions)
    {
        return !$this->can($permissions);
    }

    /**
     * @param array|string $permissions
     *
     * @return bool
     */
    public function can ($permissions)
    {

        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if (!$this->checkSingleCan($permission)) {
                    return false;
                }
            }

            return true;
        }

        if (is_string($permissions)) {
            return $this->checkSingleCan($permissions, $this->permissions);
        }

        throw new InvalidArgumentException('Permissions can only be of type array or string');
    }

    /**
     * @param string $permission
     *
     * @return bool
     */
    protected function checkSingleCan ($permission)
    {

        $permsLevel = $this->permissions;
        foreach (explode('.', $permission) as $part) {
            if (isset($permsLevel[$part])) {
                $permsLevel = $permsLevel[$part];
            } elseif (isset($permsLevel['*'])) {
                $permsLevel = $permsLevel['*'];
            }
            if ($permsLevel === '*') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $roleName
     *
     * @return bool
     */
    public function is ($roleName)
    {
        return $this->name === $roleName;
    }

    /**
     * @param array $roles
     *
     * @return mixed
     */
    public function isIn (array $roles)
    {
        return array_search($this->name, $roles) !== false;
    }

    /**
     * @return string
     */
    public function __toString ()
    {
        return $this->name;
    }

}
