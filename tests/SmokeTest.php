<?php

namespace Tests;

use Corp104\Monolog\SlackWebhookHandler;
use PHPUnit\Framework\TestCase;

class SmokeTest extends TestCase
{
    /**
     * @var SlackWebhookHandler
     */
    protected $target;

    protected function setUp()
    {
        parent::setUp();

        $this->target = new SlackWebhookHandler('some-url');
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldPassWhenInLabEnv()
    {
        $this->assertInstanceOf(SlackWebhookHandler::class, $this->target);
    }
}
