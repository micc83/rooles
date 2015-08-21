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
        $app->register('Rooles\RoleServiceProvider');

        // Bootstrap Laravel
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    }

}
