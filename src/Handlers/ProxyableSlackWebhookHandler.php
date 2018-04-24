<?php

namespace Corp104\Monolog\Handlers;

use Corp104\Support\GuzzleClientAwareInterface;
use Corp104\Support\GuzzleClientAwareTrait;
use GuzzleHttp\Psr7\Request;
use Monolog\Handler\Curl\Util;
use Monolog\Handler\SlackWebhookHandler as BaseSlackWebhookHandler;
use Monolog\Logger;

/**
 * Proxyable slack webhook handler
 */
class ProxyableSlackWebhookHandler extends BaseSlackWebhookHandler implements GuzzleClientAwareInterface
{
    use GuzzleClientAwareTrait;

    /**
     * @var string
     */
    protected $proxy;

    /**
     * @var string
     */
    protected $customWebhookUrl;

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

        $options = $this->httpOptions;

        if (null !== $this->proxy) {
            $options['proxy'] = $this->proxy;
        }

        $this->httpClient->send($request, $options);
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

        if (null !== $this->httpClient) {
            $this->sendByGuzzleHttp($postString);
            return;
        }

        $this->sendByCurl($postString);
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
     * @param null|string $proxy
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
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
