<?php

namespace Corp104\Monolog\Handlers;

use Corp104\Support\GuzzleClientAwareInterface;
use Corp104\Support\GuzzleClientAwareTrait;
use GuzzleHttp\Psr7\Request;
use Monolog\Handler\Curl\Util;
use Monolog\Handler\SlackWebhookHandler as BaseSlackWebhookHandler;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Psr18SlackWebhookHandler extends BaseSlackWebhookHandler
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $httpRequestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $httpStreamFactory;

    /**
     * @param ClientInterface $httpClient
     */
    public function setHttpClient(ClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param RequestFactoryInterface $httpRequestFactory
     */
    public function setHttpRequestFactory(RequestFactoryInterface $httpRequestFactory): void
    {
        $this->httpRequestFactory = $httpRequestFactory;
    }

    /**
     * @param StreamFactoryInterface $httpStreamFactory
     */
    public function setHttpStreamFactory(StreamFactoryInterface $httpStreamFactory): void
    {
        $this->httpStreamFactory = $httpStreamFactory;
    }

    /**
     * Overload for custom option of sending request
     *
     * @param array $record
     */
    protected function write(array $record): void
    {
        $postData = $this->getSlackRecord()->getSlackData($record);
        $postString = json_encode($postData);

        $request = $this->httpRequestFactory->createRequest('POST', $this->getWebhookUrl())
            ->withHeader('Content-type', 'application/json')
            ->withBody($this->httpStreamFactory->createStream($postString));

        $this->httpClient->sendRequest($request);
    }
}
