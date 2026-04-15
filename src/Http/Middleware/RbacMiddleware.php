<?php

namespace App\Http\Middleware;

use App\Auth\Rbac;
use App\Core\Request;
use App\Core\Response;
use App\Http\Middleware;

class RbacMiddleware implements Middleware
{
    private array $roles;

    public function __construct(array $roles = [])
    {
        $this->roles = $roles;
    }

    public function handle(Request $request, callable $next): Response
    {
        if (empty($this->roles)) {
            return $next($request);
        }

        if (!Rbac::hasAnyRole($this->roles)) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Forbidden'], 403);
            }
            return Response::redirect('/');
        }

        return $next($request);
    }
}