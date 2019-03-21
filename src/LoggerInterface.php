<?php

namespace snapsuzun\yii2logger;

use Psr\Log\LogLevel;

/**
 * Interface LoggerInterface
 * @package app\components\log
 */
interface LoggerInterface extends \Psr\Log\LoggerInterface
{
    const LEVEL_INFO = LogLevel::INFO;
    const LEVEL_ERROR = LogLevel::ERROR;
    const LEVEL_WARNING = LogLevel::WARNING;
    const LEVEL_NOTICE = LogLevel::NOTICE;
    const LEVEL_DEBUG = LogLevel::DEBUG;
    const LEVEL_CRITICAL = LogLevel::CRITICAL;
    const LEVEL_ALERT = LogLevel::ALERT;
    const LEVEL_EMERGENCY = LogLevel::EMERGENCY;

    /**
     * Set default tags for all next logs
     * @param array $tags
     * @return LoggerInterface
     */
    public function setTags(array $tags): self;

    /**
     * Get default tags
     * @return array
     */
    public function getTags(): array;

    /**
     * Push new tags to tag queue
     * @param array $tags
     * @return LoggerInterface
     */
    public function pushTags(array $tags): self;

    /**
     * Remove last added tags
     * @return array
     */
    public function popTags(): array;

    /**
     * Flush all tags from tag queue
     * @return LoggerInterface
     */
    public function flushTags(): self;
}