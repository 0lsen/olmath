<?php

use Math\Model\Number\ComplexNumber;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\RealNumber;
use Math\Parser\FormulaResult;
use Math\Parser\FormulaResultEntry;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    private $formula = "a1 = 1/2 \n 5 / 0 \n a1 + 1.5i \n ";
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
                'entries' => [
                    'a1' => [
                        'original' => '1/2',
                        'result' => [
                            's' => 1,
                            'n' => 1,
                            'd' => 2,
                            'numberType' => 'RationalNumber'
                        ],
                        'dbz' => false,
                        'variable'=> 'a1'
                    ],
                    0 => [
                        'original' => '5 / 0',
                        'dbz' => true,
                        'variable'=> ''
                    ],
                    1 => [
                        'original' => 'a1 + 1.5i',
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
                        'dbz' => false,
                        'variable'=> ''
                    ],
                ]
            ],
            'resultString' => "a1 = 1/2\nDivision by zero in: 5 / 0\na1 + 1.5i = 1/2 + 1.5 i"
        ]);

        $this->result = new FormulaResult();
        $this->result->addResult(new FormulaResultEntry('1/2', new RationalNumber(1,2,1), false, 'a1'));
        $this->result->addResult(new FormulaResultEntry('5 / 0', null, true));
        $this->result->addResult(new FormulaResultEntry('a1 + 1.5i', new ComplexNumber(new RationalNumber(1,2,1), new RealNumber(1.5))));
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
        $serverResponseBody->setResult(\Api\Mapper::mapNumberResults($this->result));
        $serverResponseBody->setResultString((string) $this->result);
        $serverResponseJson = json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($serverResponseBody));

        $this->assertEquals($this->responseJson, $serverResponseJson);

        /** @var \Swagger\Client\Model\FormulaResponseBody $clientResponseBody */
        $clientResponseBody = \Swagger\Client\ObjectSerializer::deserialize(json_decode($this->responseJson), "Swagger\Client\Model\FormulaResponseBody");
        $this->assertTrue($clientResponseBody->getOk());
        $this->assertNull($clientResponseBody->getError());
        $this->assertNull($clientResponseBody->getMessage());

        $this->assertEquals("a1 = 1/2\nDivision by zero in: 5 / 0\na1 + 1.5i = 1/2 + 1.5 i", $clientResponseBody->getResultString());

        $this->assertEquals("a1", $clientResponseBody->getResult()->getEntries()[0]->getVariable());
        $this->assertEquals("1/2", $clientResponseBody->getResult()->getEntries()[0]->getOriginal());
        $this->assertFalse($clientResponseBody->getResult()->getEntries()[0]->getDbz());
        $this->assertEquals("RationalNumber", $clientResponseBody->getResult()->getEntries()[0]->getResult()->getNumberType());
        $this->assertEquals(1, $clientResponseBody->getResult()->getEntries()[0]->getResult()->getS());
        $this->assertEquals(1, $clientResponseBody->getResult()->getEntries()[0]->getResult()->getN());
        $this->assertEquals(2, $clientResponseBody->getResult()->getEntries()[0]->getResult()->getD());

        $this->assertEquals("", $clientResponseBody->getResult()->getEntries()[1]->getVariable());
        $this->assertEquals("5 / 0", $clientResponseBody->getResult()->getEntries()[1]->getOriginal());
        $this->assertTrue($clientResponseBody->getResult()->getEntries()[1]->getDbz());

        $this->assertEquals("", $clientResponseBody->getResult()->getEntries()[2]->getVariable());
        $this->assertEquals("a1 + 1.5i", $clientResponseBody->getResult()->getEntries()[2]->getOriginal());
        $this->assertFalse($clientResponseBody->getResult()->getEntries()[2]->getDbz());
        $this->assertEquals("ComplexNumber", $clientResponseBody->getResult()->getEntries()[2]->getResult()->getNumberType());
        $this->assertEquals("RationalNumber", $clientResponseBody->getResult()->getEntries()[2]->getResult()->getR()->getNumberType());
        $this->assertEquals(1, $clientResponseBody->getResult()->getEntries()[2]->getResult()->getR()->getS());
        $this->assertEquals(1, $clientResponseBody->getResult()->getEntries()[2]->getResult()->getR()->getN());
        $this->assertEquals(2, $clientResponseBody->getResult()->getEntries()[2]->getResult()->getR()->getD());
        $this->assertEquals("RealNumber", $clientResponseBody->getResult()->getEntries()[2]->getResult()->getI()->getNumberType());
        $this->assertEquals(1.5, $clientResponseBody->getResult()->getEntries()[2]->getResult()->getI()->getR());
    }
}
