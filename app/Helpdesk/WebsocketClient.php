<?php

namespace App\Helpdesk;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Ratchet\Client;
use React\Promise\PromiseInterface;

class WebsocketClient
{
    protected string $host;
    protected string $protocol;
    protected int $port;
    protected string $uri;
    protected PromiseInterface $connection;

    public function __construct()
    {
        $this->host = env('WS_ADDRESS');
        $this->protocol = env('WS_PROTOCOL');
        $this->port = env('WS_PORT');
        $backend = env('WS_BACKEND');
        $this->uri = "$this->protocol://$this->host:$this->port$backend";
        $this->connection = Client\connect($this->uri);
    }

    public function send(array|string $message): void
    {
        if (is_array($message)) {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE);
        }
        $this->connection->then(function (Client\WebSocket $conn) use ($message) {
            $conn->send($message);
            $conn->close();
        }, function ($e) {
            echo "Could not connect: {$e->getMessage()}\n";
        });
    }

    public static function sendNotification(WebsocketsNotification $notification, $toAllConnections = false)
    {
        $instance = self::create();
        if ($toAllConnections) {
            $connections = Connection::where(['user_id' => $notification->getUserId()])->get();
            /** @var Connection $connection */
            foreach ($connections as $connection) {
                $instance->send([
                    'user_id' => $connection->user_id,
                    'conn_id' => $connection->conn_id,
                    'action' => $notification->getAction(),
                    'text' => $notification->getText()
                ]);
            }
        } else {
            $instance->send([
                'user_id' => $notification->getUserId(),
                'conn_id' => $notification->getConnId(),
                'action' => $notification->getAction(),
                'text' => $notification->getText()
            ]);
        }


    }

    public static function create(): WebsocketClient
    {
        return new self();
    }
}
