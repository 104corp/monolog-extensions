<?php

namespace Corp104\Monolog\Handlers;

use Corp104\Support\GuzzleClientAwareInterface;
use Corp104\Support\GuzzleClientAwareTrait;
use GuzzleHttp\Psr7\Request;
use Monolog\Handler\Curl\Util;
use Monolog\Handler\SlackWebhookHandler as BaseSlackWebhookHandler;

/**
 * Guzzlable slack webhook handler
 */
class GuzzlableSlackWebhookHandler extends BaseSlackWebhookHandler implements GuzzleClientAwareInterface
{
    use GuzzleClientAwareTrait;

    /**
     * Overload for custom option of sending request
     *
     * @param array $record
     */
    protected function write(array $record)
    {
        $postData = $this->getSlackRecord()->getSlackData($record);
        $postString = json_encode($postData);

        $request = $this->createGuzzleRequest(
            $this->getWebhookUrl(),
            ['Content-type' => 'application/json'],
            $postString
        );

        $httpClient = $this->getHttpClient($this->httpOptions);

        $httpClient->send($request);
    }

    /**
     * @param string $uri
     * @param array $header
     * @param string $body
     * @return Request
     */
    protected function createGuzzleRequest($uri, array $header, $body)
    {
        return new Request('POST', $uri, $header, $body);
    }
}
