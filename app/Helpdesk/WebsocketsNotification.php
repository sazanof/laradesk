<?php

namespace App\Helpdesk;

use Ratchet\ConnectionInterface;

class WebsocketsNotification
{
    protected array $request;
    protected bool $noty;
    protected string $text;
    protected string $action;
    protected ?int $connId = null;
    protected ?int $userId = null;

    public function __construct($request)
    {
        $this->action = 'notify';
        $this->connId = $request['conn_id'];
        $this->userId = $request['user_id'];
        $this->noty = true;
        $this->text = $request['text'];
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return int|null
     */
    public function getConnId(): ?int
    {
        return $this->connId;
    }

    /**
     * @return array
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @param int $connId
     */
    public function setConnId(int $connId): void
    {
        $this->connId = $connId;
    }

    /**
     * @param bool $noty
     */
    public function setNoty(bool $noty): void
    {
        $this->noty = $noty;
    }

    /**
     * @param array $request
     */
    public function setRequest(array $request): void
    {
        $this->request = $request;
    }
}
