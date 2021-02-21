<?php


namespace Framework\Core\Http;


class Middleware {

    public function handle(Request $req, $next){
        return $next($req);
    }
}