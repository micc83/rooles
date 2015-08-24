<?php

namespace Rooles;

use App\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class PermsMiddleware
 * @package Rooles
 */
class PermsMiddleware
{
    /**
     * @var RoleManager
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
     * @throws UnauthorizedHttpException
     */
    public function handle($request, Closure $next, $perms = '')
    {

        /** @var User $user */
        $user = $this->auth->user();

        if ( ! $user || $user->role->cannot($perms)) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => [
                        'code'    => 401,
                        'message' => 'Unauthorized'
                    ]
                ], 401);
            } else {
                throw new UnauthorizedHttpException('Unauthorized.');
            }
        }

        return $next($request);
    }
}
