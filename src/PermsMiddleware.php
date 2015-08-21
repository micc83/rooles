<?php

namespace Rooles;

use App\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class PermsMiddleware
 * @package App\Http\Middleware
 */
class PermsMiddleware
{
    /**
     * @var RoleRepo
     */
    protected $roleRepo;

    /**
     * @var Guard
     */
    private $auth;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param string                    $perms
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $perms = '')
    {

        /** @var User $user */
        $user = $this->auth->user();

        if ($user->role->cannot(explode('|', $perms))) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->back()->withErrors('Non sei autorizzato ad effettuare questa operazione!');
            }
        }

        return $next($request);
    }
}
