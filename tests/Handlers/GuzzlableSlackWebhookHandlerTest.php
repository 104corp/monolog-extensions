<?php

namespace Tests\Handlers;

use Corp104\Monolog\Handlers\GuzzlableSlackWebhookHandler;
use PHPUnit\Framework\TestCase;

class GuzzlableSlackWebhookHandlerTest extends TestCase
{
    /**
     * @var GuzzlableSlackWebhookHandler
     */
    protected $target;

    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new GuzzlableSlackWebhookHandler('some-url');
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
        $this->assertInstanceOf(GuzzlableSlackWebhookHandler::class, $this->target);
    }
}
