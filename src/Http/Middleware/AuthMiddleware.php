<?php

namespace App\Http\Middleware;

use App\Auth\Rbac;
use App\Core\Request;
use App\Core\Response;
use App\Core\Env;
use App\Http\Middleware;

class AuthMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        if (Rbac::isGuest()) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Unauthorized'], 401);
            }
            $baseUrl = rtrim(Env::get('BASE_URL', ''), '/');
            return Response::redirect($baseUrl . '/auth/login');
        }
        
        return $next($request);
    }
}