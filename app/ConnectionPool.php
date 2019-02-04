<?php

namespace App;

use React\Socket\ConnectionInterface;

class ConnectionPool
{

    private $connections;

    public function __construct()
    {
        $this->connections = collect();
    }

    public function registerConnection(ConnectionInterface $incomingConnection): ?Connection
    {
        $remoteAddress = $incomingConnection->getRemoteAddress();

        $connectionWrapper = new Connection($incomingConnection);
        $this->connections->put($remoteAddress, $connectionWrapper);

        return $connectionWrapper;
    }

}
