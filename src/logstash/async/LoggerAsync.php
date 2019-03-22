<?php

namespace snapsuzun\yii2logger\logstash\async;

use snapsuzun\yii2logger\logstash\Logger;
use yii\di\Instance;
use yii\queue\Queue;

/**
 * Asynchronously log saving through yii2 queue's
 * Class LoggerAsync
 * @package snapsuzun\yii2logger\logstash\async
 */
class LoggerAsync extends Logger
{
    /**
     * @var Queue|string|array
     */
    public $queue = 'queue';

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->queue = Instance::ensure($this->queue, Queue::class);
    }

    /**
     * @param array $data
     */
    protected function saveLog(array $data)
    {
        $this->queue->push(new LoggerAsyncSendJob([
            'data' => $data,
            'sender' => [
                'class' => parent::class,
                'user' => $this->user,
                'password' => $this->password,
                'tags' => $this->tags,
                'logIndex' => $this->logIndex,
                'host' => $this->host,
                'port' => $this->port,
                'defaultTags' => $this->defaultTags,
                'transportType' => $this->transportType,
                'connectionTimeout' => $this->connectionTimeout
            ]
        ]));
    }
}