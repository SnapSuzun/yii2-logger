<?php

namespace snapsuzun\yii2logger\logstash\async;

use snapsuzun\yii2logger\logstash\Logger;
use snapsuzun\yii2logger\logstash\LogstashTarget;
use yii\di\Instance;
use yii\queue\Queue;

/**
 * Class LogstashAsyncTarget
 * @package snapsuzun\yii2logger\logstash\async
 */
class LogstashAsyncTarget extends LogstashTarget
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
     *
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            $this->queue->push(new LoggerAsyncSendJob([
                'data' => $this->formatMessage($message),
                'sender' => [
                    'class' => Logger::class,
                    'user' => $this->user,
                    'password' => $this->password,
                    'logIndex' => $this->logIndex,
                    'host' => $this->host,
                    'port' => $this->port,
                    'transportType' => $this->transportType,
                ]
            ]));
        }
    }
}