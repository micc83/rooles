<?php

namespace Roole;

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
        $this->app->singleton('Roole\RoleRepo', function () {
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

        // Amministratore
        $roleRepo->create('amministratore')->grant('*');

        // Amministratore
        $roleRepo->create('operatore')->grant([
            'users.*'
        ]);

        // Agente
        $roleRepo->create('agente')->grant([
            'profile.*',
            'customers.own.read',
        ]);

        // Cliente
        $roleRepo->create('customer')->grant([
            'profile.*'
        ]);

        // Capo area
        $roleRepo->create('capoarea')->grant([
            'profile.*'
        ]);


        return $roleRepo;

    }

}
