<?php

use Illuminate\Foundation\Testing\TestCase;

/**
 * Class baseCase
 */
abstract class BaseCase extends TestCase
{

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {

        /** @var Illuminate\Foundation\Application $app */
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        // Register Roole Service Provider
        $app->register(Rooles\RoolesServiceProvider::class);

        $router = $app['router'];

        $router->middleware('role', Rooles\RoleMiddleware::class);
        $router->middleware('perms', Rooles\PermsMiddleware::class);

        return $app;
    }

    /**
     * Catch the Exception Class
     *
     * @param Closure $do
     * @param         $exceptionClass
     *
     * @return $this
     */
    public function catchException(Closure $do, $exceptionClass)
    {
        try {
            $do();
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals($exceptionClass, get_class($e->getPrevious()));
        }
        return $this;
    }

}
