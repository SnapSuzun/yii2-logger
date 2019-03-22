<?php

namespace snapsuzun\yii2logger\logstash;

use yii\helpers\VarDumper;
use yii\log\Logger as BaseLogger;
use yii\log\Target;

/**
 * Class LogstashTarget
 * @package snapsuzun\yii2logger\logstash
 */
class LogstashTarget extends Target implements LogstashInterface
{
    use LogstashTrait;

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     * @throws \yii\base\InvalidConfigException
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            $this->sendDataToLogServer($this->formatMessage($message));
        }
    }

    /**
     * @param array $message
     * @return array
     */
    public function formatMessage($message)
    {
        return $this->prepareMessage($message);
    }

    /**
     * @param array $message
     * @return array
     */
    protected function prepareMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $level = BaseLogger::getLevelName($level);
        $timestamp = date('c', $timestamp);
        $logData = [
            'level' => $level,
            'category' => $category,
            '@timestamp' => $timestamp,
            'route' => isset(\Yii::$app->controller) ? \Yii::$app->controller->getRoute() : 'application'
        ];

        $data = $this->parseText($text);
        if (is_scalar($data)) {
            $logData['message'] = $data;
        } else {
            $logData['data'] = $data;
        }

        if (isset($message[4]) === true) {
            $logData['trace'] = $message[4];
        }

        return $logData;
    }

    /**
     * @param mixed $text
     * @return array|string
     */
    protected function parseText($text)
    {
        if ($text instanceof \Throwable) {
            return [
                'message' => $text->getMessage(),
                'code' => $text->getCode(),
                'file' => $text->getFile(),
                'line' => $text->getLine(),
                'previous' => $text->getPrevious(),
                'trace' => $text->getTraceAsString()
            ];
        } elseif (is_array($text)) {
            return $text;
        } else {
            return VarDumper::export($text);
        }
    }
}