<?php

namespace App\Helpdesk;

use Symfony\Component\HttpFoundation\Request;

class TicketsFilterFromRequest
{
    protected Request $request;
    protected ?int $ticketStatus;
    protected string $sortField;
    protected string $sortDirection;
    protected int $perPage;
    protected int $page;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->ticketStatus = $request->get('status') ?? null;
        $this->sortField = $request->get('field') ?? 'created_at';
        $this->sortDirection = $request->get('dir') ?? 'desc';
        $this->perPage = $request->get('limit') ?? 25;
        $this->page = $request->get('page') ?? 1;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    /**
     * @return string
     */
    public function getSortField(): string
    {
        return $this->sortField;
    }

    /**
     * @return int
     */
    public function getTicketStatus(): ?int
    {
        return $this->ticketStatus;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @param string $sortDirection
     */
    public function setSortDirection(string $sortDirection): void
    {
        $this->sortDirection = $sortDirection;
    }

    /**
     * @param string $sortField
     */
    public function setSortField(string $sortField): void
    {
        $this->sortField = $sortField;
    }

    /**
     * @param int $ticketStatus
     */
    public function setTicketStatus(int $ticketStatus): void
    {
        $this->ticketStatus = $ticketStatus;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }
}
