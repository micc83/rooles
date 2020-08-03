<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\ExpectationFailedException;

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
     * @return Application
     */
    public function createApplication()
    {
        /** @var Illuminate\Foundation\Application $app */
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        // Register Roole Service Provider
        $app->register(Rooles\RoolesServiceProvider::class);

        /** @var Illuminate\Routing\Router $router */
        $router = $app['router'];

        $router->aliasMiddleware('role', Rooles\RoleMiddleware::class);
        $router->aliasMiddleware('perms', Rooles\PermsMiddleware::class);

        return $app;
    }

    /**
     * Visit a page and assert it throws an exception with the name provided
     *
     * @param string $page
     * @param string $exceptionClass
     *
     * @return TestResponse
     */
    public function visitAndCatchException($page, $exceptionClass)
    {
        return $this->catchException(function () use ($page) {
            return $this->get($page);
        }, $exceptionClass);
    }

    /**
     * Catch the Exception Class name and assert its equal to the one given
     *
     * @param Closure $do
     * @param string  $exceptionClass
     *
     * @return mixed
     */
    public function catchException(Closure $do, $exceptionClass)
    {
        try {
            return $do();
        } catch (ExpectationFailedException $e) {
            $this->assertEquals($exceptionClass, get_class($e->getPrevious()));
        }

        return $this;
    }

}
