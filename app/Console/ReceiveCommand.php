<?php

namespace App\Console;

use App\Servers\ReceiveServer;
use Illuminate\Console\Command;

class ReceiveCommand extends Command
{

    protected $signature = "tcp:receive {--l|listen=tcp://0.0.0.0} {--p|port=11000}";

    protected $description = "Receive TCP packets";

    protected $listenPort = 11000;

    protected $listenAddress = "tcp://0.0.0.0";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->init();

        $loop = app('react.loop');
        $server = new ReceiveServer($loop, $this->listenAddress . ':' . $this->listenPort);
        $server->start();

        $loop->run();
    }

    private function init()
    {
        $this->listenPort = $this->option('port');
        $this->listenAddress = $this->option('listen');
    }
}
