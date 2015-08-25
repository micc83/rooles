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
        foreach ((array)$permissions as $permission) {
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
     * @param array|string $query
     *
     * @return bool
     */
    public function evaluate($query)
    {
        foreach ($this->parseQuery($query) as $andPermissions) {
            foreach ($andPermissions as $orPermission) {
                if ($result = $this->evaluatePermission($orPermission)) {
                    break;
                }
            }
            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * Parse the query
     *
     * If a string is provided it will be converted
     * to array. AND and OR operator will be parsed
     * in order to get a multidimensional array of
     * the requested permissions.
     *
     * @param array|string $query
     * @return array
     */
    protected function parseQuery($query)
    {
        return $this->parseOrOperator(
            $this->parseAndOperator((array)$query)
        );
    }

    /**
     * Parse the query AND conditions
     *
     * It's applied when passing array of permissions
     * or when using the "&" operator
     *
     * @param array $query
     * @param string $operator
     *
     * @return array
     */
    protected function parseAndOperator(array $query, $operator = '&')
    {
        $permissions = [];
        foreach ($query as $permissionsGroup) {
            foreach (explode($operator, $permissionsGroup) as $andPerms) {
                $permissions[] = $andPerms;
            }
        }
        return $permissions;
    }

    /**
     * Parse the query OR conditions
     *
     * It's applied when using the "|" operator
     *
     * @param array $query
     * @param string $operator
     *
     * @return array
     */
    protected function parseOrOperator(array $query, $operator = '|')
    {
        $permissions = [];
        foreach ($query as $key => $orPerms) {
            foreach (explode($operator, $orPerms) as $permission) {
                $permissions[$key][] = $permission;
            }
        }
        return $permissions;
    }

    /**
     * Check the availability of a single permission
     *
     * @param string $permission
     *
     * @return bool
     */
    protected function evaluatePermission($permission)
    {
        $permsLevel = $this->permissions;

        foreach ($this->explodePermission($permission) as $part) {
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