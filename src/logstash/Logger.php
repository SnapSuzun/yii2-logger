<?php

namespace snapsuzun\yii2logger\logstash;


use snapsuzun\yii2logger\BaseLogger;
use yii\base\InvalidConfigException;

/**
 * Class Logger
 * @package snapsuzun\yii2logger\logstash
 */
class Logger extends BaseLogger implements LogstashInterface
{
    use LogstashTrait;

    /**
     * @param string $level
     * @param mixed $message
     * @param array $context
     * @throws InvalidConfigException
     */
    public function log($level, $message, array $context = [])
    {
        $this->createLog($level, $message, $context);
    }

    /**
     * @param string $level
     * @param mixed $message
     * @param array $context
     * @throws InvalidConfigException
     */
    protected function createLog(string $level, $message, array $context = [])
    {
        if (!$this->beforeCreate($level, $message, $context)) {
            return;
        }
        $logData = [
            'level' => $level,
            'route' => $this->getCurrentRoute()
        ];
        $data = $this->formatMessage($message, $context);
        if (is_scalar($data)) {
            $logData['message'] = $data;
        } else {
            $logData['data'] = $data;
        }
        if (!empty($context)) {
            $logData['context'] = $context;
        }
        if (!empty($tags = $this->getCompiledTags())) {
            $logData['tags'] = $tags;
        }
        $this->saveLog($logData);
        $this->afterCreate($level, $message, $context);
    }

    /**
     * @param array $data
     * @throws InvalidConfigException
     */
    protected function saveLog(array $data)
    {
        $this->sendDataToLogServer($data);
    }
}