<?php

namespace snapsuzun\yii2logger\logstash\async;


use snapsuzun\yii2logger\logstash\LogstashInterface;
use yii\base\BaseObject;
use yii\di\Instance;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class LoggerAsyncSendJob
 * @package app\components\log\logstash
 */
class LoggerAsyncSendJob extends BaseObject implements JobInterface
{
    /**
     * @var array
     */
    public $data;

    /**
     * @var array|string
     */
    public $sender;

    /**
     * @var LogstashInterface
     */
    private $_sender;

    /**
     * @param Queue $queue
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        if (empty($this->_sender)) {
            $this->_sender = Instance::ensure($this->sender, LogstashInterface::class);
        }
        $this->_sender->sendDataToLogServer($this->data);
    }
}