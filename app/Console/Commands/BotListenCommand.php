<?php

namespace App\Console\Commands;

use App\Libraries\BotAPI;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class BotListenCommand extends Command
{
    protected $signature = 'bot:listen';
    protected $description = 'Listen to bot events via SSE';
    protected $currentEvent = null;
    protected $currentEventData = null;

    public function handle()
    {
        $bot = new BotAPI();

        $this->info('Starting bot...');
        $this->info('Connecting to SSE...');

        $stream = $bot->listen([-1]);

        if (!is_resource($stream)) {
            $this->error('Failed to open a valid stream resource.');
            return CommandAlias::FAILURE;
        }

        // 1. Отключаем внутреннюю буферизацию PHP
        stream_set_read_buffer($stream, 0);

        // 2. КРИТИЧЕСКИ ВАЖНО: Переводим сокет в НЕБЛОКИРУЮЩИЙ режим
        // Теперь fgets() или fread() не будут вешать скрипт, если в сети пусто
        stream_set_blocking($stream, false);

        $this->info('Connected! Listening for messages...');

        $this->currentEvent = null;
        $this->currentEventData = null;
        $buffer = '';

        // Бесконечный цикл опроса неблокирующего сокета
        while (true) {
            // Проверяем, не закрылся ли сокет со стороны сервера
            if (feof($stream)) {
                $this->warn('Stream connection lost.');
                break;
            }

            // Читаем доступные байты из сокета (не блокирует выполнение)
            $chunk = fread($stream, 8192);

            if ($chunk === false) {
                $this->error('Error reading from stream.');
                break;
            }

            // Если в сокете пусто, спим 50 миллисекунд (0.05 сек), чтобы не грузить процессор на 100%
            if ($chunk === '') {
                usleep(50000);
                continue;
            }

            // Накапливаем сырые данные в буфер
            $buffer .= $chunk;

            // Построчно разбираем накопленный буфер
            while (($newlinePos = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $newlinePos);
                $buffer = substr($buffer, $newlinePos + 1);
                $line = rtrim($line, "\r");

                // Обработка ПИНГ-строк
                if ($line === 'PING') {
                    //dump("PING");
                    continue;
                }

                // Пустая строка — маркер отправки накопленного SSE-события
                if ($line === '') {
                    if ($this->currentEventData !== null) {
                        $eventName = $this->currentEvent ?? 'message';

                        //dump("MESS_RECEIVED:", $this->currentEventData);
                        $this->processEvent($eventName, $this->currentEventData, $bot);
                    }
                    $this->currentEvent = null;
                    $this->currentEventData = null;
                    continue;
                }

                // Игнорируем комментарии SSE
                if (strpos($line, ':') === 0) {
                    continue;
                }

                // Парсим стандартные SSE поля
                $parts = explode(':', $line, 2);
                $field = $parts[0];
                $value = isset($parts[1]) ? $parts[1] : '';

                if (strpos($value, ' ') === 0) {
                    $value = substr($value, 1);
                }

                switch ($field) {
                    case 'event':
                        $this->currentEvent = $value;
                        break;
                    case 'data':
                        if ($this->currentEventData === null) {
                            $this->currentEventData = $value;
                        } else {
                            $this->currentEventData .= "\n" . $value;
                        }
                        break;
                }
            }
        }

        fclose($stream);
        $this->warn('Stream closed.');
        return CommandAlias::SUCCESS;
    }

    private function processEvent(string $eventType, string $dataLine, BotAPI $bot): void
    {
        // Игнорируем PING
        if ($eventType === 'PING') {
            return;
        }

        if ($eventType !== 'MESSAGE' || !$dataLine) {
            return;
        }

        // Парсим data
        $data = json_decode($dataLine, true);

        if (!$data || !isset($data['content'])) {
            return;
        }

        // Парсим content (второй уровень)
        $content = json_decode($data['content'], true);

        if (!$content) {
            return;
        }

        $workspaceId = $content['workspaceId'] ?? -1;
        $groupId = $content['groupId'] ?? null;
        $messages = $content['payload']['messages'] ?? [];

        if (!$groupId || empty($messages)) {
            return;
        }

        foreach ($messages as $message) {
            $senderId = $message['senderId'] ?? null;
            $messageId = $message['id'] ?? null;

            if (!$senderId || !$messageId) {
                continue;
            }

            $text = $message['message'] ?? '';
            $this->info("📩 [{$messageId}] From: {$senderId} -> \"{$text}\"");

            // Обрабатываем сообщение
            $bot->handleMessage($message, (int)$workspaceId, (int)$groupId);
        }
    }
}
