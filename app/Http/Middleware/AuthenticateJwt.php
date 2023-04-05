<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticateJwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * Check if JWT is valid
         *
         * @var User $user
         */
        $user = Auth::guard('api')->user();

        // Check if user is admin before accessing the route
        if ($request->is('api/v1/admin/*')) {
            if (!$user->is_admin) {
                throw new UnauthorizedHttpException('Not allowed');
            }
        }

        return $next($request);
    }
}
