<?php

namespace snapsuzun\yii2logger\events;


use yii\base\Event;

/**
 * Class CreateLogEvent
 * @package app\components\log\events
 */
class CreateLogEvent extends Event
{
    /**
     * @var mixed
     */
    public $message;

    /**
     * @var array
     */
    public $context = [];

    /**
     * @var string
     */
    public $level;

    /**
     * @var array
     */
    public $tags = [];
}