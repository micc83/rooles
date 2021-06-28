<?php

namespace Rooles;

use InvalidArgumentException;
use Rooles\Contracts\Role as RoleContract;
use Rooles\Contracts\RoleRepository;
use Throwable;
use UnexpectedValueException;

/**
 * Class RoleManager
 * @package Rooles
 */
class RoleManager implements RoleRepository
{
    /**
     * Roles storage array
     */
    protected array $roles = [];

    /** @inheritDoc */
    public function getOrCreate(string $roleName): Role
    {
        try {
            return $this->get($roleName);
        } catch (Throwable $e) {
            return $this->create($roleName);
        }
    }

    /** @inheritDoc */
    public function get(string $roleName): Role
    {
        $roleName = mb_strtolower($roleName);

        if (empty($roleName)) {
            return $this->getOrCreate('Default');
        }

        if (isset($this->roles[$roleName])) {
            return $this->roles[$roleName];
        }

        throw new InvalidArgumentException('Role not found');
    }

    /** @inheritDoc */
    public function create(string $roleName): Role
    {
        return $this->add(
            new Role($roleName, new Permissions())
        );
    }

    /** @inheritDoc */
    public function add(RoleContract $role): Role
    {
        if (isset($this->roles[$role->id()])) {
            throw new UnexpectedValueException('Duplicated role!');
        }

        $this->roles[$role->id()] = $role;

        return $role;
    }
}
