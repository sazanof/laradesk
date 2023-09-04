<?php

namespace App\Console\Commands;

use App\Helpdesk\WebsocketServerHandler;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;

use Illuminate\Console\Command;

class WebsocketServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helpdesk:websocket-server-run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run websockets server';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if (!env('WS_PORT')) {
            exit('WS_PORT not found');
        }

        $address = env('WS_ADDRESS', 'ws://127.0.0.1');
        $port = env('WS_PORT', 8080);

        $this->info(sprintf('Websocket server starts on port %04d', $port));

        $ws = new WsServer(new WebsocketServerHandler($this));

        $server = IoServer::factory(
            new HttpServer($ws),
            $port,
            $address
        );
        $ws->enableKeepAlive($server->loop, 10);
        $server->run();
    }
}
