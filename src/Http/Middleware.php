<?php

namespace App\Http;

use App\Core\Request;
use App\Core\Response;

interface Middleware
{
    public function handle(Request $request, callable $next): Response;
}