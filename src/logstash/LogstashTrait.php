<?php

namespace snapsuzun\yii2logger\logstash;

use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

/**
 * Trait LogstashTrait
 * @package app\components\log\logstash
 */
trait LogstashTrait
{
    /**
     * @var string
     */
    public $host;

    /**
     * @var int
     */
    public $port = 80;

    /**
     * @var string
     */
    public $user;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $logIndex = '';

    /**
     * @var string
     */
    public $transportType = LogstashInterface::TRANSPORT_HTTP;


    /**
     * @param array $data
     * @return bool
     * @throws InvalidConfigException
     */
    public function sendDataToLogServer(array $data): bool
    {
        switch ($this->transportType) {
            case LogstashInterface::TRANSPORT_HTTP:
                $client = new Client();
                $client->setTransport(CurlTransport::class);
                $url = trim($this->host) . ($this->port ? ':' . $this->port : '80') . '/' . urlencode($this->logIndex);
                $httpRequest = $client->createRequest()
                    ->setMethod('PUT')
                    ->setUrl($url)
                    ->setFormat(Client::FORMAT_JSON)
                    ->setData($data);
                if ($this->user && $this->password) {
                    $httpRequest->setHeaders(['authorization' => 'Basic ' . base64_encode("{$this->user}:{$this->password}")]);
                }
                $response = $httpRequest->send();
                return $response->isOk;
                break;
            case LogstashInterface::TRANSPORT_SOCKET:
                $fp = fsockopen($this->host, $this->port ?: -1, $errorNumber, $error, 30);
                $data['@index'] = $this->logIndex;
                $bytes = fwrite($fp, json_encode($data));
                fclose($fp);
                return $bytes > 0;
                break;
            default:
                throw new InvalidConfigException("Incorrect transport type.");
        }
    }
}