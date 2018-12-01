<?php

namespace Api\Middleware;


use Illuminate\Database\Capsule\Manager;
use Slim\Http\Request;
use Slim\Http\Response;

class DBConnectMiddleware {
    public function __invoke(Request $request, Response $response, $next)
    {
        try {
            $settings = require PROJECT_ROOT . '/app/config/db.php';
            $connection = new Manager();
            $connection->addConnection($settings);
            $connection->bootEloquent();
        } catch (\Throwable $t) {
            return $response->withStatus(500);
        }

        return $next($request, $response);
    }
}