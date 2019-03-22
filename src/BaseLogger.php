<?php

namespace snapsuzun\yii2logger;

use snapsuzun\yii2logger\events\CreateLogEvent;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class BaseLogger
 * @package snapsuzun\yii2logger
 */
abstract class BaseLogger extends Component implements LoggerInterface
{
    const EVENT_BEFORE_CREATE_LOG = 'beforeCreateLog';
    const EVENT_AFTER_CREATE_LOG = 'afterCreateLog';

    /**
     * List of default tags that are including to all logs
     * @var array
     */
    public $defaultTags = [];

    /**
     * @var array
     */
    protected $additionalTagsQueue = [];

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = [])
    {
        $this->log(static::LEVEL_INFO, $message, $context);
    }

    /**
     * @param mixed $message
     * @param array $context
     */
    public function error($message, array $context = [])
    {
        $this->log(static::LEVEL_ERROR, $message, $context);
    }

    /**
     * @param mixed $message
     * @param array $context
     */
    public function warning($message, array $context = [])
    {
        $this->log(static::LEVEL_WARNING, $message, $context);
    }

    /**
     * @param mixed $message
     * @param array $context
     */
    public function notice($message, array $context = [])
    {
        $this->log(static::LEVEL_NOTICE, $message, $context);
    }

    /**
     * @param mixed $message
     * @param array $context
     */
    public function debug($message, array $context = [])
    {
        $this->log(static::LEVEL_DEBUG, $message, $context);
    }

    /**
     * @param mixed $message
     * @param array $context
     */
    public function critical($message, array $context = [])
    {
        $this->log(static::LEVEL_CRITICAL, $message, $context);
    }

    /**
     * @param mixed $message
     * @param array $context
     */
    public function alert($message, array $context = [])
    {
        $this->log(static::LEVEL_ALERT, $message, $context);
    }

    /**
     * @param mixed $message
     * @param array $context
     */
    public function emergency($message, array $context = [])
    {
        $this->log(static::LEVEL_EMERGENCY, $message, $context);
    }

    /**
     * @param array $tags
     * @return LoggerInterface
     */
    public function setTags(array $tags): LoggerInterface
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return LoggerInterface
     */
    public function pushTags(array $tags): LoggerInterface
    {
        $this->additionalTagsQueue[] = $tags;
        return $this;
    }

    /**
     * @return array
     */
    public function popTags(): array
    {
        return array_pop($this->additionalTagsQueue);
    }

    /**
     * @return LoggerInterface
     */
    public function flushTags(): LoggerInterface
    {
        $this->additionalTagsQueue = [];
        return $this;
    }

    /**
     * @param array $tags
     * @return array
     */
    protected function getCompiledTags(array $tags = []): array
    {
        $compiledTags = ArrayHelper::merge($this->defaultTags, $this->tags);
        foreach ($this->additionalTagsQueue as $tagsFromQueue) {
            $compiledTags = ArrayHelper::merge($compiledTags, $tagsFromQueue);
        }
        return ArrayHelper::merge($compiledTags, $tags);
    }

    /**
     * @param string $level
     * @param mixed $message
     * @param array $context
     * @return bool
     */
    protected function beforeCreate(string $level, $message, array $context)
    {
        $event = new CreateLogEvent([
            'message' => $message,
            'context' => $context,
            'level' => $level,
            'tags' => $this->getCompiledTags()
        ]);
        $this->trigger(static::EVENT_BEFORE_CREATE_LOG, $event);
        return !$event->handled;
    }

    /**
     * @param string $level
     * @param mixed $message
     * @param array $context
     */
    protected function afterCreate(string $level, $message, array $context)
    {
        $this->trigger(static::EVENT_AFTER_CREATE_LOG, new CreateLogEvent([
            'message' => $message,
            'context' => $context,
            'level' => $level,
            'tags' => $this->getCompiledTags()
        ]));
    }

    /**
     * @return string
     */
    protected function getCurrentRoute(): string
    {
        return isset(\Yii::$app->controller) ? \Yii::$app->controller->getRoute() : 'application';
    }

    /**
     * @param mixed $message
     * @param array $context
     * @return mixed
     */
    protected function formatMessage($message, array $context = [])
    {
        $type = gettype($message);
        if ($message instanceof \Throwable) {
            return [
                'message' => $message->getMessage(),
                'code' => $message->getCode(),
                'file' => $message->getFile(),
                'line' => $message->getLine(),
                'previous' => $message->getPrevious(),
                'trace' => $message->getTraceAsString()
            ];
        } else {
            switch ($type) {
                case 'array':
                    return $message;
                case 'string':
                    return $this->interpolate($message, $context);
                default:
                    return VarDumper::export($message);
            }
        }
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function interpolate(string $message, array $context = [])
    {
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        return strtr($message, $replace);
    }
}