# Rooles

### Why another Laravel RBAC (Role based access control) ?!?

Well, good point! Lately even *Taylor Otwell* is working on a custom ACL system to be shipped with (I guess as a separated package) **Laravel 5.2** so what's the point on creating a new one?
Well it's all about complexity. Most of the ACL systems out here such as [romanbican/roles](https://github.com/romanbican/roles), [kodeine/laravel-acl](https://github.com/kodeine/laravel-acl) or [Sentinel](https://cartalyst.com/manual/sentinel/) are packed with tons of amazing features... which most of the time I'm not using! :D

That's why I thought to build a minimal **Laravel roles and permissions manager** that provides a very simple RBAC implementation on top of the **default Laravel Auth System**. 
Each user can be assigned a single Role, while permissions for each Role are stored in a single config file. With the package are provided a very intuitive and well documented API, a Trait to check permissions directly on the Eloquent User Model and two Middlewares to easily protect routes and Controllers.

However, as your application grown, you might need a more complex ACL system, that's why the package comes with a couple of Contracts that you can leverage to improve or replace functionalities at need. You can see **Rooles** not only as a fully working RBAC but also as a *starting point to develop your own custom roles and permission manager*.

### Setup

Run the following from your terminal from within the path containing the Laravel `composer.json` file:

```sh
$ composer require micc83/rooles
```

Open `config/app.php` and add the following line at the end of the providers array:

```php
Rooles\RoolesServiceProvider::class
```
    
Run the following command from your terminal to publish the migration (it will simply add a `role` column to the default Users table) and the config files:

```sh
$ php artisan vendor:publish
```

In order to be able to use route and Controllers middlewares (so to be able to filter who's able to access a given route or Controller method) open `App/Http/Kernel.php` and add the following lines at the end of the `$routeMiddleware` array:

```php
'perms' => \Rooles\PermsMiddleware::class,
'role'  => \Rooles\RoleMiddleware::class,
```
    
As **Rooles** works on top of the default *Auth* system of Laravel and with the *Eloquent* User Model you must add the `Rooles\Traits\UserRole` trait to the User Class located in `App/User.php` as follow:

```php
use \Rooles\Traits\UserRole;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword, UserRole;

    // ...
}
```

### Setting up users role

Only a single Role can be assigned to each User. You can hardcode the role inside the User Eloquent model adding the role attribute as follow:

```php
protected $attributes = [
    'role' => 'admin'
];
```
    
Or run the provided migration to add the `role` column to the Users Table so to be able to change Users role at runtime:

```php
$user = User::find(1);
$user->role = 'admin';
$user->save();
```

### Setting up permissions

All the permissions for any given role are set in the `config/rooles.php` file as follow:

```php
<?php return [
    'roles' => [
        'default' => []
        'admin' => [
            'grant' => '*'
        ],
        'editor' => [
            'grant' => [
                'posts.*',
                'users.*.read',
                'users.*.ban',
                'comments.*',
                'profile.*'
            ],
            'deny' => [
                'users.admin.ban',
                'posts.delete',
                'comments.delete'
            ]
        ]
    ]
];
```
    
The **wildcard character "\*"** is used to define a whole subset of available permissions. For example if we take in consideration the grant `users.*.ban`, that means that editors can ban any group of users ( `users.reader`, `users.author` etc... ) but not `users.admin` as the permission has been denied in the deny array. 

> The wildcard character at the end of a permission string can be omitted as `users` and `users.*` are the same.

The `default` role is applied to any user which has no role applied and provides no permissions unless differently stated in the config file.

You can also create roles and handle permissions manually. Here's an example:

```php
app()->make(\Rooles\Contracts\RoleRepository::class)
     ->getOrCreate('customer')
     ->grant(['cart.*', 'products.buy'])
     ->deny('cart.discount');
```

### Checking for User permissions

From within your Controller methods or wherever you feel comfortable you can check for a given user permissions as follow:

```php
$user = User::find(1);
if ($user->can('comments.post')){
    // Do something...
}
```

The same to check the logged in user permissions:

```php
public function index(Illuminate\Contracts\Auth\Guard $auth) {
    
    if ( $auth->user->can('users.list') ){
        // Do something...
    }
    
}
```

The API exposes a convenient method to negate a permissions assertion:

```php
if ( $user->cannot('users.list') ) redirect()->to('dashboard');
```
    
You can evaluate multiple assertions passing an array through:

```php
if ( $user->can(['users.list', 'users.read']) ) // Do something when the user has both the permissions (AND)
```

There are also two convenient operator to use with the can/cannot assertions:

```php
if ( $user->can('users.list&users.read') ) // Do something when the user has both the permissions (& > AND)
if ( $user->can('users.list|users.read') ) // Do something when the user has one of the requested permissions (| > OR)
```
    
Multiple operators can ben be joined together but mind that AND operators have always priority over OR operators.

### Checking for User role

You can make a more general assertion checking for the user role:

```php
if ( $user->role->is('admin') ) echo 'Hello Boss';
```
    
Or check if the user role is in a given range:

```php
if ( $user->role->isIn(['lamer', 'trool']) ) echo 'Hello Looser';
``` 

You can also get the User role name using one of the following syntax:

```php
// If in a string context:
echo $user->role;
// Otherwise:
if ($user->role->name() === 'admin') // Do something
```

### Protect routes and Controllers through Middlewares

**Rooles** provides two Middlewares to protect both routes and Controllers.

To protect routes by User Role you can use the **role Middleware**:

```php
Route::get('admin/users/', [
    'middleware' => [
        'auth',
        'role:admin|editor', // Give access to both admins and editors
    ],
    function () {
        return view('admin.users.index');
    }
]);
```
    
In order to check for user permissions on a route you can use the **perms Middleware** as follow:

```php
Route::get('admin/users/', [
    'middleware' => [
        'auth',
        'perms:users.list|users.edit', // Give access to users with users.list OR users.edit permissions
    ]
    function () {
        return view('admin.users.index');
    }
]);
```

In both case you'll have probably noticed that I'm calling the **Auth middleware** as the user must be logged in in order to check its Role and permissions.

Most of the times you'll be probably being dealing with routes groups, in that case you can simply:

```php
// Route Group
Route::group([
    'middleware' => [
        'auth',
        'role:admin|editor' // Give access to both admins and editors
    ]
], function () {
    Route::resource('users', 'UserController');
    Route::resource('posts', 'PostController');
});
```

Middlewares can also be used in Controllers as follow:

```php
class UserController extends Controller
{

    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @param UserRepo $users
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
        $this->middleware('perms:users', ['except' => 'show']);
    }
```

Here we are saying that in order to access any controller method we must have a role that provides all the `users` permissions but we don't need any permission to show user profiles. You can find a better documentation on Controller Middlewares on the official [Laravel website](http://laravel.com/docs/5.0/controllers#controller-middleware).

#### Handling middlewares HTTP error responses

**Rooles** middlewares handles error responses differently depending on the nature of the request. For Ajax requests they will respond with a JSON Object and a `401` status code as follow:

```json
{
    "error" : {
        "code" : 401,
        "message" : "Unauthorized"
    }
}
```

So that you can intercept it in JavaScript as follow:

```js
if ('error' in response) console.log(response.error.message);
```

For normal requests in case of error a `Rooles\UnauthorizedHttpException` is thrown, which by default will result in the default error page with a `401` status code but it can be customized from the render method in `app/Exceptions/Handler.php` as follow:

```php
public function render($request, Exception $e)
{
    //...
    if ($e instanceof \Rooles\UnauthorizedHttpException) {
        redirect()->back()->with('message', 'You don\'t have the needed permissions to perform this action!');
    }
    return parent::render($request, $e);
}
```

### Contributions

I'd be glad if you'd like to contribute to the project however I'd ask not to implement new features but to improve the few existing ones (improve patterns etc). Each PR must follow [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standards and pass all the existing tests (or add furthers when needed). 
