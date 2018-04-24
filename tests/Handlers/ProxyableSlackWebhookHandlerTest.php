<?php

namespace Tests\Handlers;

use Corp104\Monolog\Handlers\ProxyableSlackWebhookHandler;
use PHPUnit\Framework\TestCase;

class ProxyableSlackWebhookHandlerTest extends TestCase
{
    /**
     * @var ProxyableSlackWebhookHandler
     */
    protected $target;

    protected function setUp()
    {
        parent::setUp();

        $this->target = new ProxyableSlackWebhookHandler('some-url');
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldPass()
    {
        $this->assertInstanceOf(ProxyableSlackWebhookHandler::class, $this->target);
    }
}
