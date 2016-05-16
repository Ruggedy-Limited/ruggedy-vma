<?php

namespace app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class JsonApiMiddleware
{
    const PARSED_METHODS = [
        'POST', 'PUT', 'PATCH'
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (in_array($request->getMethod(), self::PARSED_METHODS) && !empty($request->getContent())) {
            $request->merge(json_decode($request->getContent(), true));
        }

        return $next($request);
    }
}