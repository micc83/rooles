<?php

namespace Rooles;

use Illuminate\Support\ServiceProvider;
use Rooles\Contracts\RoleRepository;

/**
 * Class RoleServiceProvider
 * @package App\Crm\Permission
 */
class RoleServiceProvider extends ServiceProvider
{

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RoleRepository::class, function () {
            return $this->registerRoles(new RoleRepo);
        });
    }

    /**
     * Permissions
     *
     * @param RoleRepository $roleRepo
     *
     * @return RoleRepo
     */
    public function registerRoles(RoleRepository $roleRepo)
    {
        return $roleRepo;
    }

}
