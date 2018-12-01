<?php

use Math\Model\Number\ComplexNumber;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\RealNumber;
use Math\Parser\NumberResult;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    private $formula = "1/2 + 1.5i";
    private $decimalPoint = ".";
    private $groupSeparator = ".";
    private $requestJson;
    private $responseJson;
    private $result;

    function setUp()
    {
        $this->requestJson = json_encode([
            'formula' => $this->formula,
            'decimalPoint' => $this->decimalPoint,
            'groupSeparator' => $this->groupSeparator
        ]);

        $this->responseJson = json_encode([
            'ok' => true,
            'result' => [
                'original' => $this->formula,
                'result' => [
                    'r' => [
                        's' => 1,
                        'n' => 1,
                        'd' => 2,
                        'numberType' => 'RationalNumber',
                    ],
                    'i' => [
                        'r' => 1.5,
                        'numberType' => 'RealNumber'
                    ],
                    'numberType' => 'ComplexNumber'
                ],
                'dbz' => false
            ]
        ]);

        $number = new ComplexNumber(new RationalNumber(1,2,1), new RealNumber(1.5));
        $this->result = new NumberResult($this->formula, $number);
        parent::setUp();
    }

    function testRequestBody()
    {
        $clientRequestBody = new \Swagger\Client\Model\FormulaRequestBody();
        $clientRequestBody->setFormula($this->formula);
        $clientRequestBody->setDecimalPoint($this->decimalPoint);
        $clientRequestBody->setGroupSeparator($this->groupSeparator);
        $clientRequestJson = json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($clientRequestBody));

        $this->assertEquals($this->requestJson, $clientRequestJson);

        /** @var \OpenAPI\Client\Model\FormulaRequestBody $serverRequestBody */
        $serverRequestBody = \Swagger\Client\ObjectSerializer::deserialize(json_decode($clientRequestJson), "Swagger\Client\Model\FormulaRequestBody");

        $this->assertEquals($this->formula, $serverRequestBody->getFormula());
        $this->assertEquals($this->decimalPoint, $serverRequestBody->getDecimalPoint());
        $this->assertEquals($this->groupSeparator, $serverRequestBody->getGroupSeparator());
    }

    function testResponseBody()
    {
        $serverResponseBody = new \Swagger\Client\Model\FormulaResponseBody();
        $serverResponseBody->setOk(true);
        $serverResponseBody->setResult(\Api\Mapper::mapNumberResult($this->result));
        $serverResponseJson = json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($serverResponseBody));

        $this->assertEquals($this->responseJson, $serverResponseJson);

        $clientResponseBody = \Swagger\Client\ObjectSerializer::deserialize(json_decode($this->responseJson), "Swagger\Client\Model\FormulaResponseBody");
        $this->assertTrue($clientResponseBody->getOk());
        $this->assertNull($clientResponseBody->getError());
        $this->assertNull($clientResponseBody->getMessage());
        $this->assertEquals("ComplexNumber", $clientResponseBody->getResult()->getResult()->getNumberType());
        $this->assertEquals("RationalNumber", $clientResponseBody->getResult()->getResult()->getR()->getNumberType());
        $this->assertEquals(1, $clientResponseBody->getResult()->getResult()->getR()->getS());
        $this->assertEquals(1, $clientResponseBody->getResult()->getResult()->getR()->getN());
        $this->assertEquals(2, $clientResponseBody->getResult()->getResult()->getR()->getD());
        $this->assertEquals("RealNumber", $clientResponseBody->getResult()->getResult()->getI()->getNumberType());
        $this->assertEquals(1.5, $clientResponseBody->getResult()->getResult()->getI()->getR());
    }
}
