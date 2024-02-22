<?php

namespace App\Helpers;

use App\Helpdesk\TicketParticipant;
use App\Helpdesk\TicketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RequestBuilder
{
    protected Builder $builder;
    protected Request $request;
    protected string $criteria;

    protected string $sortField;
    protected string $sortDirection;
    protected int $perPage;
    protected int $page;
    protected ?array $approvalsIds = null;
    protected ?array $requestersIds = null;
    protected ?array $observersIds = null;
    protected ?string $text = null;
    protected ?int $categoryId = null;
    protected ?int $userId = null;
    protected ?int $departmentId = null;

    public function __construct(Request $request, Builder $builder)
    {
        $this->request = $request;
        $this->builder = $builder;
        $this->criteria = $request->get('criteria');
        $this->sortField = $request->get('field') ?? 'created_at';
        $this->sortDirection = $request->get('dir') ?? 'desc';
        $this->perPage = $request->get('limit') ?? 25;
        $this->page = $request->get('page') ?? 1;
        $participants = $request->get('participants') ?? [];
        $this->approvalsIds = $participants['approvals'] ?? null;
        $this->requestersIds = $participants['requesters'] ?? null;
        $this->observersIds = $participants['observers'] ?? null;
        $this->text = $request->get('text') ?? null;
        $this->categoryId = $request->get('category_id') ?? null;
        $this->userId = $request->get('user_id') ?? Auth::id();
        $this->departmentId = $request->get('department') ?? null;

        $this
            ->addDependencyTables()
            ->parseCriteria()
            ->addCategory()
            ->addDepartment()
            ->addSearchCriteria()
            ->addApprovals()
            ->addRequesters()
            ->addObservers()
            ->setOrder();
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @return $this
     */
    public function addDependencyTables(): static
    {
        $this->builder
            ->select(['tickets.*']);
        if ($this->criteria === 'my' ||
            $this->criteria === 'approval' ||
            !empty($this->approvalsIds) ||
            !empty($this->observersIds)) {
            $this->builder
                ->selectRaw('tp.ticket_id as tp_ticket_id,tp.role as tp_role, tp.user_id as tp_user_id')
                ->join('ticket_participants as tp', 'tickets.id', 'tp.ticket_id');
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function setOrder(): static
    {
        $this->builder->orderBy(
            $this->sortField,
            $this->sortDirection
        );
        return $this;
    }

    public function addDepartment()
    {
        if (!empty($this->departmentId)) {
            if (is_numeric($this->departmentId)) {
                $department = [$this->departmentId];
                $this->builder->whereIn('tickets.department_id', $department);
            }
        }
        return $this;
    }

    public function addCategory()
    {
        if (!empty($this->categoryId)) {
            $this->builder->where('tickets.category_id', $this->categoryId);
        }
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    /**
     * @return $this
     */
    public function addApprovals(): static
    {
        if (!empty($this->approvalsIds)) {
            $this->builder
                ->where('tp.role', TicketParticipant::APPROVAL)
                ->whereIn('tp.user_id', $this->approvalsIds);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function addObservers(): static
    {
        if (!empty($this->observersIds)) {
            $this->builder
                ->where('tp.role', TicketParticipant::OBSERVER)
                ->whereIn('tp.user_id', $this->observersIds);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function addRequesters(): static
    {
        if (!empty($this->requestersIds)) {
            $this->builder->whereIn('tickets.user_id', $this->requestersIds);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function addSearchCriteria(): static
    {
        if (!empty($this->text)) {
            $this->builder->where(function (Builder $query) {
                $query
                    ->orWhere('tickets.subject', 'LIKE', '%' . $this->text . '%')
                    ->orWhere('tickets.content', 'LIKE', '%' . $this->text . '%');
            });
        }
        return $this;
    }

    public function parseCriteria()
    {
        switch ($this->criteria) {
            case 'my':
                $this->builder
                    ->whereIn('status', TicketStatus::OPEN)
                    ->where('tp.role', TicketParticipant::ASSIGNEE)
                    ->where('tp.user_id', $this->userId); // не находит в queue передавать аргументом
                break;
            case 'all':
                break;
            case 'open':
                $this->builder->whereIn('tickets.status', TicketStatus::OPEN);
                break;
            case 'approval':
                $this->builder
                    //->whereNotIn('status', [TicketStatus::APPROVED, TicketStatus::CLOSED, TicketStatus::SOLVED])
                    //->where('need_approval', 1)
                    ->whereNotIn('status', [TicketStatus::CLOSED, TicketStatus::SOLVED])
                    ->where('tp.role', TicketParticipant::APPROVAL)
                    ->where('tp.user_id', $this->userId);
                break;
            case 'closed':
                $this->builder->whereIn('tickets.status', TicketStatus::NOT_OPEN);
                break;
            case 'sent':
                $this->builder->where('tickets.user_id', $this->userId);
                break;

        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function get()
    {
        return $this->builder->get();
    }

    public static function results(Request $request, Builder $builder)
    {
        return new self($request, $builder);
    }
}
