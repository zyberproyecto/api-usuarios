<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin {
    public function handle(Request $r, Closure $next){
        $u = $r->user();
        if (!$u || ($u->rol ?? '') !== 'admin') {
            return response()->json(['ok'=>false,'error'=>'Forbidden'], 403);
        }
        return $next($r);
    }
}