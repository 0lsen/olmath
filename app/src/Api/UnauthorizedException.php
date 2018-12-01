<?php

namespace Api;


use Slim\Middleware\TokenAuthentication\UnauthorizedExceptionInterface;

class UnauthorizedException implements UnauthorizedExceptionInterface
{

}