<?php

namespace Rooles;

use Illuminate\Support\ServiceProvider;
use Rooles\Contracts\RoleRepository;

/**
 * Class RoolesServiceProvider
 * @package Rooles
 */
class RoolesServiceProvider extends ServiceProvider
{

    /**
     * Boot the service provider
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/assets/config.php' => config_path('rooles.php'),
        ]);

        $this->publishes([
            __DIR__ . '/assets/migration.php' => database_path('migrations/' . date('Y_m_d_His_') . 'add_role_column_to_user_table.php')
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/assets/config.php', 'rooles'
        );
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RoleRepository::class, function () {
            return $this->registerRoles(new RoleManager);
        });
    }

    /**
     * Permissions
     *
     * @param RoleRepository $roleRepo
     *
     * @return RoleRepository
     */
    public function registerRoles(RoleRepository $roleRepo)
    {
        $roles = config('rooles.roles');

        foreach ($roles as $roleName => $permissions) {
            $role = $roleRepo->getOrCreate($roleName);
            if (array_key_exists('grant', $permissions)) {
                $role->grant($permissions['grant']);
            }
            if (array_key_exists('deny', $permissions)) {
                $role->deny($permissions['deny']);
            }
        }

        return $roleRepo;
    }

}
