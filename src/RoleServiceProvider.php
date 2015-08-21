<?php

namespace Rooles;

use Illuminate\Support\ServiceProvider;

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
        $this->app->singleton('Rooles\RoleRepo', function () {
            return $this->registerRoles(new RoleRepo);
        });
    }

    /**
     * Permissions
     *
     * @param RoleRepo $roleRepo
     *
     * @return RoleRepo
     */
    public function registerRoles(RoleRepo $roleRepo)
    {
        return $roleRepo;
    }

}
