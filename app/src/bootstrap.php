<?php

use Api\DB\Apikey;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Middleware\TokenAuthentication;

$authenticator = function (Request $request, \Slim\Middleware\TokenAuthentication $tokenAuth) {
    $token = $tokenAuth->findToken($request);
    if (!Apikey::find($token)) {
        throw new Exception();
    }
};

$c = new \Slim\Container();
$c['notFoundHandler'] = function ($c) {return function (Request $request, Response $response) use ($c) {return $response->withStatus(404);};};

$app = new Slim\App($c);

$app->post('/formula/evaluate', function(Request $request, Response $response, $args) {
    $body = json_decode(json_encode($request->getParsedBody()));
    /** @var \Swagger\Client\Model\FormulaRequestBody $requestBody */
    $requestBody = \Swagger\Client\ObjectSerializer::deserialize($body, "Swagger\Client\Model\FormulaRequestBody");
    if (!is_null($requestBody) && !empty($requestBody->getFormula())) {
        $parser = new \Math\Parser\NumberParser(
            $requestBody->getDecimalPoint() ? $requestBody->getDecimalPoint() : '.',
            $requestBody->getGroupSeparator() ? $requestBody->getGroupSeparator() : ','
        );
        $responseBody = new \Swagger\Client\Model\FormulaResponseBody();
        try {
            $result = $parser->evaluate($requestBody->getFormula());
            $responseBody->setOk(true);
            $responseBody->setResult(\Api\Mapper::mapNumberResult($result));
            $responseBody->setResultString((string) $result->getResult());
        } catch (\Throwable $t) {
            $responseBody->setOk(false);
            $responseBody->setError(get_class($t));
            $responseBody->setMessage($t->getMessage());
        }
        $responseObject = \Swagger\Client\ObjectSerializer::sanitizeForSerialization($responseBody);
        //Todo: write to log
        return $response->withJson($responseObject);
    } else {
        return $response->withStatus(400);
    }
});

$app
    ->add(new TokenAuthentication([
        'path' => '/',
        'authenticator' => $authenticator
    ]))
    ->add(new \Api\Middleware\DBConnectMiddleware())
    ->add(new \Api\Middleware\UriBasePathMiddleware());

$app->run();