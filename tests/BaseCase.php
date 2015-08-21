<?php

/**
 * Class baseCase
 */
class BaseCase extends PHPUnit_Framework_TestCase {

    /**
     * Setup base test case
     */
    public function setUp () {

        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        // Register Roole Service Provider
        $app->register('Roole\RoleServiceProvider');

        // Bootstrap Laravel
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    }

}
