<?php

namespace snapsuzun\yii2logger\logstash;

/**
 * Interface LogstashInterface
 * @package snapsuzun\yii2logger\logstash
 */
interface LogstashInterface
{
    const TRANSPORT_HTTP = 'transport_http';
    const TRANSPORT_SOCKET = 'transport_socket';

    /**
     * @param array $data
     * @return bool
     */
    public function sendDataToLogServer(array $data): bool;
}