<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BotAPI
{
    public string $token;
    public string $baseUrl;
    public string $sseUrl;

    public function __construct()
    {
        $this->token = config('tdm.token');
        $this->baseUrl = config('tdm.url');
        $this->sseUrl = config('tdm.sse');
    }

    /**
     * Подключиться к SSE и начать слушать события
     */
    public function listen(array $workspaceIds = [-1])
    {
        $url = $this->sseUrl . '/api/v2/events/bot';

        $headers = [
            'Authorization' => $this->token,
            'WorkspaceIds' => implode(',', $workspaceIds),
            'Accept' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
        ];

        Log::info('Connecting to SSE', ['url' => $url]);

        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $url, [
            'headers' => $headers,
            'stream' => true,
            'timeout' => 0,
            'read_timeout' => 0, // Важно для бесконечных стримов
        ]);

        // Извлекаем чистый PHP ресурс сокета
        return $response->getBody()->detach();
    }

    /**
     * Парсит HTML с помощью регулярных выражений (быстрее, но проще)
     */
    public function parseHtml(string $html): array
    {
        // 1. Заменяем <br> на \n
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);

        // 2. Находим все ссылки <a href="...">text</a>
        $links = [];
        $html = preg_replace_callback('/<a\s+href=["\']([^"\']*)["\'][^>]*>(.*?)<\/a>/is', function ($m) use (&$links) {
            $links[] = ['url' => trim($m[1]), 'text' => trim($m[2])];
            return '###LINK_' . (count($links) - 1) . '###';
        }, $html);

        // 3. Находим все теги форматирования
        $tags = [];
        $html = preg_replace_callback('/<(b|strong|i|em|u|s|strike|code|tt)>(.*?)<\/\1>/is', function ($m) use (&$tags) {
            $tags[] = ['type' => $m[1], 'text' => trim($m[2])];
            return '###TAG_' . (count($tags) - 1) . '###';
        }, $html);

        // 4. Удаляем все остальные теги
        $message = strip_tags($html);

        // 5. Восстанавливаем теги и ссылки
        $entities = [];

        // Сначала ссылки
        foreach ($links as $i => $link) {
            $placeholder = '###LINK_' . $i . '###';
            $pos = mb_strpos($message, $placeholder);
            if ($pos !== false) {
                $text = $link['text'];
                $message = str_replace($placeholder, $text, $message);
                $entities[] = [
                    'textUrl' => [
                        'offset' => $pos,
                        'length' => mb_strlen($text),
                        'url' => $link['url'],
                        'disablePreview' => false
                    ]
                ];
            }
        }

        // Потом теги форматирования
        $typeMap = [
            'b' => 'bold', 'strong' => 'bold',
            'i' => 'italic', 'em' => 'italic',
            'u' => 'underline',
            's' => 'strike', 'strike' => 'strike',
            'code' => 'monospace', 'tt' => 'monospace'
        ];

        foreach ($tags as $i => $tag) {
            $placeholder = '###TAG_' . $i . '###';
            $pos = mb_strpos($message, $placeholder);
            if ($pos !== false) {
                $text = $tag['text'];
                $message = str_replace($placeholder, $text, $message);
                $type = $typeMap[$tag['type']] ?? null;
                if ($type) {
                    $entities[] = [
                        $type => [
                            'offset' => $pos,
                            'length' => mb_strlen($text)
                        ]
                    ];
                }
            }
        }

        // 6. Сортируем entities по offset
        usort($entities, function ($a, $b) {
            $aOff = current($a)['offset'] ?? 0;
            $bOff = current($b)['offset'] ?? 0;
            return $aOff <=> $bOff;
        });

        return [
            'message' => $message,
            'entities' => $entities
        ];
    }


    /**
     * Отправить текстовое сообщение
     */
    public function sendText(int $workspaceId, int $groupId, string $message): int
    {
        $response = Http::withHeaders([
            'Authorization' => $this->token,
            'Content-Type' => 'application/json',
        ])->withOptions([
            'verify' => false,
        ])->post("{$this->baseUrl}/botapi/v1/messages/sendTextMessage/{$workspaceId}/{$groupId}", [
            'message' => $message,
            'clientRandomId' => time() . rand(100000, 999999),
        ]);

        if (!$response->successful()) {
            throw new \Exception("Send failed: " . $response->body());
        }

        return $response->json('messageId');
    }

    /**
     * Отправить HTML сообщение (с парсингом тегов)
     */
    public function sendHtml(int $workspaceId, int $groupId, string $html): int
    {
        $parsed = $this->parseHtml($html);

        return $this->sendFormatted(
            $workspaceId,
            $groupId,
            $parsed['message'],
            $parsed['entities']
        );
    }

    /**
     * Отправить форматированное сообщение
     */
    public function sendFormatted(int $workspaceId, int $groupId, string $message, array $entities = []): int
    {
        $response = Http::withHeaders([
            'Authorization' => $this->token,
            'Content-Type' => 'application/json',
        ])->withOptions([
            'verify' => false, // ← ОТКЛЮЧАЕМ ПРОВЕРКУ SSL
        ])->post("{$this->baseUrl}/botapi/v1/messages/sendTextMessage/{$workspaceId}/{$groupId}", [
            'message' => $message,
            'entities' => $entities,
            'clientRandomId' => time() . rand(100000, 999999),
        ]);

        if (!$response->successful()) {
            throw new \Exception("Send failed: " . $response->body());
        }

        return $response->json('messageId');
    }

    /**
     * Подтвердить обработку сообщения
     */
    public function confirm(int $workspaceId, int $groupId, int $lastMessageId): void
    {
        Http::withHeaders([
            'Authorization' => $this->token,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/botapi/v1/messages/confirm/{$workspaceId}/{$groupId}", [
            'lastMessageId' => $lastMessageId,
        ]);
    }

    /**
     * Получить все группы бота
     */
    public function getGroups(): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->token,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/botapi/v1/groups/getAllUserGroupStates");

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }

    /**
     * Получить информацию о пользователе
     */
    public function getUserInfo(int $workspaceId, int $userId): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->token,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/botapi/v1/groups/getUserGroupState/{$workspaceId}/3268744994442506");

        dump($response->getBody()->getContents());

        if (!$response->successful()) {
            return null;
        }

        $members = $response->json();

        foreach ($members as $member) {
            if (isset($member['user']['id']) && $member['user']['id'] == $userId) {
                return $member['user'];
            }
        }

        return null;
    }

    /**
     * Получить ID P2P чата с пользователем
     */
    public function getP2PGroupId(int $workspaceId, int $userId): ?int
    {
        $groups = $this->getGroups();

        foreach ($groups as $group) {
            if (($group['group']['type'] ?? '') === 'P2P') {
                $peer1 = $group['group']['peer1Id'] ?? null;
                $peer2 = $group['group']['peer2Id'] ?? null;

                if ($peer1 == $userId || $peer2 == $userId) {
                    return $group['groupId'];
                }
            }
        }

        return null;
    }

    /**
     * Обработать сообщение (получить email и ответить)
     */
    public function handleMessage(array $message, int $workspaceId, int $groupId): void
    {
        $senderId = $message['senderId'] ?? null;
        $messageId = $message['id'] ?? null;

        if (!$senderId || !$messageId) {
            return;
        }

        // Получаем email пользователя
        $userInfo = $this->getUserInfo($workspaceId, $senderId);


        if ($userInfo) {
            $email = $userInfo['email'] ?? 'не найден';
            $name = trim(($userInfo['firstName'] ?? '') . ' ' . ($userInfo['lastName'] ?? ''));
            $name = $name ?: 'Пользователь';

            // Отправляем ответ с email и ID
            $this->sendText($workspaceId, $groupId,
                "👋 Привет, {$name}!\n" .
                "📧 Email: {$email}\n" .
                "🆔 ID: {$senderId}"
            );
        }

        // Подтверждаем обработку
        $this->confirm($workspaceId, $groupId, $messageId);
    }
}
