<?php

use PHPUnit\Framework\TestCase;

class LiveTest extends TestCase
{
    function testAsd()
    {
        $config = new \Swagger\Client\Configuration();
        $config->setApiKey('Authorization', require '../app/config/live_test.php');

        $api = new \Swagger\Client\Api\DefaultApi(null, $config);

        $request = new \Swagger\Client\Model\FormulaRequestBody();
        $request->setFormula('1+1');

        $response = $api->formulaEvaluatePost($request);
        $this->assertEquals("1+1 = 2", $response->getResultString());
    }
}