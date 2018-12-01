<?php

namespace Api\Middleware;


use Slim\Http\Uri;

class UriBasePathMiddleware
{
    function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, $next) {
        /** @var Uri $uri */
        $uri = $request->getUri();
        $basepath = $uri->getBasePath();
        if (strpos($basepath, '/phpythagoras') !== false) {
            $newUri = $uri->withBasePath('')->withPath(str_replace('/phpythagoras', '', $basepath));
            $newRequest = $request->withUri($newUri);
            $response = $next($newRequest, $response);
        } else {
            $response = $next($request, $response);
        }
        return $response;
    }
}