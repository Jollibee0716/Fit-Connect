<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetAuditUser
{
    public function handle(Request $request, Closure $next): Response
    {
        DB::statement('SET @app_user_id = ?', [$request->user()?->id]);

        return $next($request);
    }
}