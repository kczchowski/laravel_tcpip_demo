<?php

namespace App;

use React\Socket\ConnectionInterface;

/**
 * Class Connection
 * @package App
 */
class Connection
{

    /** @var \React\Socket\ConnectionInterface */
    private $rawConnection;

    /** @var string */
    private $localAddress;

    /** @var string */
    private $remoteAddress;

    /**
     * Connection constructor.
     * @param \React\Socket\ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface &$connection)
    {
        $this->rawConnection = $connection;
        $this->localAddress = $connection->getLocalAddress();
        $this->remoteAddress = $connection->getRemoteAddress();

        $this->registerConnectionEvents();
    }

    /**
     * Register handlers for all connection events
     */
    protected function registerConnectionEvents()
    {
        $this->rawConnection->on('data', $this->onConnectionData());
        $this->rawConnection->on('close', $this->onConnectionClose());
        $this->rawConnection->on('end', $this->onConnectionEnd());
        $this->rawConnection->on('error', $this->onConnectionError());
    }

    /**
     * @return \Closure
     */
    protected function onConnectionData(): \Closure
    {
        return function ($data) {
            $data = trim($data);
            $this->echo($data, '>>');
        };
    }

    /**
     * @return \Closure
     */
    protected function onConnectionClose(): \Closure
    {
        return function () {
            $this->echo("Connection closed", 'x-');
        };
    }

    /**
     * @return \Closure
     */
    protected function onConnectionEnd(): \Closure
    {
        return function () {
            $this->echo("Connection ended", '-x');
        };
    }

    /**
     * @return \Closure
     */
    protected function onConnectionError(): \Closure
    {
        return function () {
            $this->echo("Connection error", '!!');
        };
    }

    /**
     * Write message to stdout
     * @param string $message
     * @param string $prefix
     */
    private function echo(string $message, string $prefix = "--")
    {
        echo sprintf("%.4f", microtime(true)) . " | " . $prefix . ' ' . $message . PHP_EOL;
    }

}
