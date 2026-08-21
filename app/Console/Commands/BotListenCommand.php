<?php

namespace App\Console\Commands;

use App\Libraries\BotAPI;
use Illuminate\Console\Command;

class BotListenCommand extends Command
{
    protected $signature = 'bot:listen';
    protected $description = 'Listen to bot events via SSE';

    public function handle()
    {
        $bot = new BotAPI();

        $this->info('Starting bot...');
        $this->info('Connecting to SSE...');

        // Подключаемся к SSE
        $response = $bot->listen([-1]);
        $stream = $response->getBody();

        $this->info('Connected! Listening for messages...');

        // Читаем поток
        while (!$stream->eof()) {
            $line = $stream->read(1024);

            // Ищем событие MESSAGE
            if (strpos($line, 'event: MESSAGE') !== false) {
                // Читаем следующую строку с данными
                $dataLine = $stream->read(1024);

                if (strpos($dataLine, 'data:') === 0) {
                    $json = trim(substr($dataLine, 5));
                    $event = json_decode($json, true);

                    if ($event) {
                        $workspaceId = $event['workspaceId'] ?? -1;
                        $groupId = $event['groupId'] ?? null;
                        $message = $event['message'] ?? [];

                        if ($groupId && !empty($message)) {
                            $this->info('New message from user: ' . ($message['senderId'] ?? 'unknown'));

                            // Обрабатываем
                            $bot->handleMessage($message, (int)$workspaceId, (int)$groupId);
                        }
                    }
                }
            }
        }
    }
}
