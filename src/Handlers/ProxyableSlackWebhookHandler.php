<?php

namespace Corp104\Monolog\Handlers;

use Monolog\Handler\Curl\Util;
use Monolog\Handler\SlackWebhookHandler as BaseSlackWebhookHandler;

/**
 * Proxyable slack webhook handler
 */
class ProxyableSlackWebhookHandler extends BaseSlackWebhookHandler
{
    /**
     * @var string
     */
    protected $proxy;

    /**
     * @param string $proxy
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
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

        $ch = curl_init();

        $options = [
            CURLOPT_URL => $this->getWebhookUrl(),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-type: application/json'],
            CURLOPT_POSTFIELDS => $postString,
        ];

        if (defined('CURLOPT_SAFE_UPLOAD')) {
            $options[CURLOPT_SAFE_UPLOAD] = true;
        }

        if (null !== $this->proxy) {
            $options[CURLOPT_PROXY] = $this->proxy;
        }

        curl_setopt_array($ch, $options);

        Util::execute($ch);
    }
}
