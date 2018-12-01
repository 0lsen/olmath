<?php

class FeatureTest extends \There4\Slim\Test\WebTestCase
{
    function setup()
    {
        if (!defined('PROJECT_ROOT')) {
            define('PROJECT_ROOT', __DIR__.'/../..');
            include_once PROJECT_ROOT.'/vendor/autoload.php';
        }

        $authMock = Mockery::mock('alias:Api\DB\Apikey');
        $authMock
            ->shouldReceive('find')
            ->with('authorised')
            ->andReturn(new \Api\DB\Apikey());
        $authMock
            ->shouldReceive('find')
            ->with('unauthorised')
            ->andReturnNull();

        $logMock = Mockery::mock('alias:Api\DB\Log');
        $logMock
            ->shouldReceive('create');

        parent::setup();
    }

    function getSlimInstance()
    {
        include PROJECT_ROOT.'/app/src/bootstrap.php';
        return $app;
    }

    function test200()
    {
        $this->client->post('/formula/evaluate', ['formula' => '1 + 1'], ['HTTP_AUTHORIZATION' => 'Bearer authorised']);
        $this->assertEquals(200, $this->client->response->getStatusCode());
    }

    function test400()
    {
        $this->client->post('/formula/evaluate', ['foo' => 'bar'], ['HTTP_AUTHORIZATION' => 'Bearer authorised']);
        $this->assertEquals(400, $this->client->response->getStatusCode());
    }
}