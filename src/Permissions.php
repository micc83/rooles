<?php

namespace Rooles;

use Rooles\Contracts\Permissions as PermissionsContract;

/**
 * Class Permissions
 * @package Rooles
 */
class Permissions implements PermissionsContract
{

    /**
     * Permissions storage
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * Set permissions from string or array
     *
     * @param string|array $permissions
     * @param string $value
     */
    public function set($permissions, $value)
    {

        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as $permission) {
            $this->setPermission($permission, $value);
        }

    }

    /**
     * Store a single permission
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
     * Check permissions string or array
     *
     * @param array|string $permissions
     *
     * @return bool
     */
    public function check($permissions)
    {

        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $result = false;

        foreach ($permissions as $permissionGroups) {
            $result = false;
            foreach (explode('&', $permissionGroups) as $permissionGroup) {
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
     * Check the availability of a single permission
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

}