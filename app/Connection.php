<?php

namespace App;

use App\Coffee\RequestFactory;
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

    protected $requestFactory;

    /**
     * Connection constructor.
     * @param \React\Socket\ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface &$connection)
    {
        $this->rawConnection = $connection;
        $this->localAddress = $connection->getLocalAddress();
        $this->remoteAddress = $connection->getRemoteAddress();

        $this->requestFactory = new RequestFactory();

        $this->registerConnectionEvents();
    }

    /**
     * Write the data to the connection
     * @param $data
     */
    protected function write($data)
    {
        $this->echo($data, '>>');
        $this->rawConnection->write($data . PHP_EOL);
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

            $request = $this->requestFactory->make($data);
            if($request !== null){
                $this->echo($data, '<<');
                if($request->hasResponse()){
                    $this->write($request->getResponse());
                }
            }else{
                $this->echo('Unknown format: '.$data, '!!');
            }
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
