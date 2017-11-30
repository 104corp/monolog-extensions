<?php

namespace Corp104\Monolog;

use Corp104\Support\GuzzleClientAwareInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Monolog\Handler\Curl\Util;
use Monolog\Handler\SlackWebhookHandler as BaseSlackWebhookHandler;
use Monolog\Logger;

/**
 * Proxyable slack webhook handler
 */
class SlackWebhookHandler extends BaseSlackWebhookHandler implements GuzzleClientAwareInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $proxy;

    /**
     * @var string
     */
    protected $customWebhookUrl;

    /**
     * @param string $uri
     * @param array $header
     * @param string $body
     * @return Request
     */
    protected function createGuzzleRequest($uri, array $header, $body): Request
    {
        return new Request('POST', $uri, $header, $body);
    }

    /**
     * Using curl to send request
     *
     * @param $postString
     */
    public function sendByCurl($postString)
    {
        $ch = curl_init();

        $options = [
            CURLOPT_URL => $this->customWebhookUrl,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-type: application/json'],
            CURLOPT_POSTFIELDS => $postString,
        ];

        if (null !== $this->proxy) {
            $options[CURLOPT_PROXY] = $this->proxy;
        }

        if (\defined('CURLOPT_SAFE_UPLOAD')) {
            $options[CURLOPT_SAFE_UPLOAD] = true;
        }

        curl_setopt_array($ch, $options);

        Util::execute($ch);
    }

    /**
     * Using GuzzleHttp to send request
     *
     * @param string $postString
     */
    public function sendByGuzzleHttp($postString)
    {
        $request = $this->createGuzzleRequest(
            $this->customWebhookUrl,
            ['Content-type' => 'application/json'],
            $postString
        );

        $options = [
            'timeout' => 3,
        ];

        if (null !== $this->proxy) {
            $options['proxy'] = $this->proxy;
        }

        $this->client->send($request, $options);
    }

    /**
     * Overload for custom option of sending request
     *
     * @param array $record
     */
    protected function write(array $record)
    {
        $postData = $this->getSlackRecord()->getSlackData($record);
        $postString = json_encode($postData);

        if (null === $this->client) {
            $this->sendByCurl($postString);
        }

        $this->sendByGuzzleHttp($postString);
    }

    public function __construct(
        $webhookUrl,
        $channel = null,
        $username = null,
        $useAttachment = true,
        $iconEmoji = null,
        $useShortAttachment = false,
        $includeContextAndExtra = false,
        $level = Logger::CRITICAL,
        $bubble = true,
        array $excludeFields = []
    ) {
        parent::__construct(
            $webhookUrl,
            $channel,
            $username,
            $useAttachment,
            $iconEmoji,
            $useShortAttachment,
            $includeContextAndExtra,
            $level,
            $bubble,
            $excludeFields
        );

        $this->customWebhookUrl = $webhookUrl;
    }

    /**
     * @param Client $client
     */
    public function setHttpClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param null|string $proxy
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }
}
