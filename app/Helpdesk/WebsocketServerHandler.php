<?php

namespace App\Helpdesk;

use App\Console\Commands\WebsocketServerCommand;
use App\Jobs\ExportJob;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\Socket\Connection;

/**
 * @deprecated
 */
class WebsocketServerHandler implements MessageComponentInterface
{
    protected WebsocketServerCommand $command;
    /** @var array|ConnectionInterface[] $connections */
    protected array $connections = [];

    public function __construct(WebsocketServerCommand $command)
    {
        $this->command = $command;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        /**
         * @var Connection $conn
         * @var $request Request
         */
        $request = $conn->httpRequest;
        $resourceId = $conn->resourceId;
        $remoteAddress = $conn->remoteAddress;
        if ($request->getUri()->getPath() === env('WS_FRONT')) {
            // CONNECTION FROM FRONTEND
            parse_str($request->getUri()->getQuery(), $query);
            if (!empty($query) && isset($query['user_id'])) {
                // Add connection to database
                $data = ['user_id' => $query['user_id'], 'conn_id' => $resourceId];
                \App\Models\Connection::insertOrIgnore($data);
                $this->send($conn, $data);
                $this->connections[$resourceId] = $conn;
                $this->command->info(sprintf('[%d] New client wants to open connection (frontend).', $resourceId));
            } else {
                $conn->close();
            }

        } else if ($request->getUri()->getPath() === env('WS_BACKEND')) {
            // CONNECTION FROM BACKEND
            /*$this->send($conn, ['conn_id' => $resourceId]);*/
            $this->command->info(sprintf('[%d] New client wants to open connection (backend).', $resourceId));
        } else {
            $this->command->error(sprintf('[%d] connection not authorized', $resourceId));
            $conn->close();
        }

    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $message = json_decode($msg, true);
        $userId = isset($message['user_id']) ? (int)$message['user_id'] : null;
        if (is_null($userId)) {
            Log::error('[WS] Null user id in ' . $msg);
        } else {
            if (!empty($message['conn_id'])) {
                $connId = (int)$message['conn_id'];
                switch ($message['action']) {
                    case 'notify':
                        $this->connections[$connId]->send(json_encode([
                            'noty' => true,
                            'action' => $message['action'],
                            'text' => $message['text']
                        ]));
                        break;
                }
            }
        }

    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->connections[$conn->resourceId]);
        \App\Models\Connection::where(['conn_id' => $conn->resourceId])->delete();
        $this->command->warn(sprintf('[%d] clients close connection', $conn->resourceId));
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }

    public function send(ConnectionInterface $connection, array $data)
    {
        $connection->send(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
