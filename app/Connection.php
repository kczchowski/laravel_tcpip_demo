<?php

namespace App;

use React\Socket\ConnectionInterface;

class Connection
{

    private $rawConnection;

    private $localAddress;

    private $remoteAddress;

    public function __construct(ConnectionInterface &$connection)
    {
        $this->rawConnection = $connection;
        $this->localAddress = $connection->getLocalAddress();
        $this->remoteAddress = $connection->getRemoteAddress();

        $this->registerConnectionEvents();
    }

    protected function registerConnectionEvents()
    {
        $this->rawConnection->on('data', $this->onConnectionData());

        $this->rawConnection->on('close', $this->onConnectionClose());

        $this->rawConnection->on('end', $this->onConnectionEnd());

        $this->rawConnection->on('error', $this->onConnectionError());
    }

    protected function onConnectionData()
    {
        return function ($data) {
            $data = trim($data);
            $this->echo($data, '>>');
        };
    }

    protected function onConnectionClose()
    {
        return function () {
            $this->echo("Connection closed", 'x-');
        };
    }

    protected function onConnectionEnd()
    {
        return function () {
            $this->echo("Connection ended", '-x');
        };
    }

    protected function onConnectionError()
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
