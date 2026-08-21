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

        return $client->request('GET', $url, [
            'headers' => $headers,
            'stream' => true,
            'timeout' => 0,
        ]);
    }

    /**
     * Отправить текстовое сообщение
     */
    public function sendText(int $workspaceId, int $groupId, string $message): int
    {
        $response = Http::withHeaders([
            'Authorization' => $this->token,
            'Content-Type' => 'application/json',
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
        ])->post("{$this->baseUrl}/botapi/v1/workspaces/getMembers", [
            'WorkspaceId' => $workspaceId,
        ]);

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
