<?php

namespace Tests\Handlers;

use Corp104\Monolog\Handlers\GuzzlableSlackWebhookHandler;
use Corp104\Monolog\Handlers\Psr18SlackWebhookHandler;
use PHPUnit\Framework\TestCase;

class Psr18SlackWebhookHandlerTest extends TestCase
{
    /**
     * @var Psr18SlackWebhookHandler
     */
    protected $target;

    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new Psr18SlackWebhookHandler('some-url');
    }

    protected function tearDown(): void
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldPass(): void
    {
        $this->assertInstanceOf(Psr18SlackWebhookHandler::class, $this->target);
    }
}
