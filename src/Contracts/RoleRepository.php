<?php

namespace Rooles\Contracts;

use InvalidArgumentException;

/**
 * Interface RoleRepository
 * @package Rooles\Contracts
 */
interface RoleRepository
{
    /**
     * Get an existing role or create a new one with the given name
     */
    public function getOrCreate(string $roleName): Role;

    /**
     * Get the role with the given name and return a "default" role i
     * if the given name is empty.
     *
     * @throws InvalidArgumentException
     */
    public function get(string $roleName): Role;

    /**
     * Create a new role
     */
    public function create(string $roleName): Role;

    /**
     * Add an existing role object to the repository
     */
    public function add(Role $role): Role;
}
