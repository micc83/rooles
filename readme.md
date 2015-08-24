# Rooles
## Very simple package to handle Roles and Permissions in Laravel 5


### Setup

Run the following from your terminal:

    composer require micc83/rooles

Open config/app.php and add the following line at the end of the providers array:

    Rooles\RoolesServiceProvider::class
    
In order to publish the migration and the config file run the following command from your terminal:

    php artisan vendor:publish

In order to be able to use route middlewares (so to be able to filter who's able to access a given route) open App/Http/Kernel.php and add the following lines at the end of the $routeMiddleware array:

    'perms' => \Rooles\PermsMiddleware::class,
    'role' => \Rooles\RoleMiddleware::class,
    
As Rooles works on top of the default Auth system of Laravel and with the Eloquent User Model you must add the Rooles\UserRole trait to the User Class located in App/User.php as follow:

    class User extends Model implements AuthenticatableContract, CanResetPasswordContract
    {
    
        use Authenticatable, CanResetPassword, \Rooles\Traits\UserRole;
    
        // ...
    }

