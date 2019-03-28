<?php

namespace snapsuzun\yii2logger;

use Psr\Log\LogLevel;

/**
 * Interface LoggerInterface
 * @package snapsuzun\yii2logger
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
     * System is unusable.
     *
     * @param string|array|object $message
     * @param array  $context
     *
     * @return void
     */
    public function emergency($message, array $context = array());

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string|array|object $message
     * @param array  $context
     *
     * @return void
     */
    public function alert($message, array $context = array());

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string|array|object $message
     * @param array  $context
     *
     * @return void
     */
    public function critical($message, array $context = array());

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string|array|object $message
     * @param array  $context
     *
     * @return void
     */
    public function error($message, array $context = array());

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string|array|object $message
     * @param array  $context
     *
     * @return void
     */
    public function warning($message, array $context = array());

    /**
     * Normal but significant events.
     *
     * @param string|array|object $message
     * @param array  $context
     *
     * @return void
     */
    public function notice($message, array $context = array());

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string|array|object $message
     * @param array  $context
     *
     * @return void
     */
    public function info($message, array $context = array());

    /**
     * Detailed debug information.
     *
     * @param string|array|object $message
     * @param array  $context
     *
     * @return void
     */
    public function debug($message, array $context = array());

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string|array|object $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array());


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