<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token is Invalid'
                ], 401);
            } elseif ($e instanceof TokenExpiredException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token is Expired'
                ], 401);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Authorization Token not found'
                ], 401);
            }
        }

        return $next($request);
    }
}