<?php

namespace App\Helpers;

use App\Helpdesk\Participant;
use App\Helpdesk\TicketStatus;
use Carbon\Carbon;
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
    protected ?Carbon $start = null;
    protected ?Carbon $end = null;

    public function __construct(Request $request, Builder $builder)
    {
        $start = $request->get('start');
        $end = $request->get('end');
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
        $this->start = !is_null($start) ? Carbon::parse($start) : null;
        $this->end = !is_null($end) ? Carbon::parse($end) : null;

        $this
            ->addDependencyTables()
            ->parseCriteria()
            ->addCategory()
            ->addSearchCriteria()
            ->addApprovals()
            ->addRequesters()
            ->addObservers()
            ->setOrder()
            ->setDates();
        if ($this->criteria !== 'sent') {
            $this->addDepartment();
        }
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
            $this->criteria === 'observer' ||
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

    /**
     * @return $this
     */
    public function setDates(): static
    {
        if ($this->start instanceof Carbon && $this->end instanceof Carbon) {
            $this->builder->whereBetween('tickets.created_at', [$this->start, $this->end]);
        } else {
            if ($this->start instanceof Carbon) {
                $this->builder->where('tickets.created_at', '>=', $this->start);
            } else if ($this->end instanceof Carbon) {
                $this->builder->where('tickets.created_at', '<=', $this->end);
            }
        }
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
                ->where('tp.role', Participant::APPROVAL)
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
                ->where('tp.role', Participant::OBSERVER)
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
                    ->where('tp.role', Participant::ASSIGNEE)
                    ->where('tp.user_id', $this->userId) // не находит в queue передавать аргументом
                    ->whereNull('tp.deleted_at');
                break;
            case 'all':
                break;
            case 'open':
                $this->builder->whereIn('tickets.status', TicketStatus::OPEN);
                break;
            case 'approval':
                $this->builder
                    //"select `tickets`.*, tp.ticket_id as tp_ticket_id,tp.role as tp_role, tp.user_id as tp_user_id from `tickets`
                    // inner join `ticket_participants` as `tp` on `tickets`.`id` = `tp`.`ticket_id`
                    // where `status` not in (5, 4) and `tp`.`role` = 3 and `tp`.`user_id` = 1 and `tickets`.`deleted_at` is null"

                    //->whereNotIn('status', [TicketStatus::APPROVED, TicketStatus::CLOSED, TicketStatus::SOLVED])
                    //->where('need_approval', 1)
                    ->whereNotIn('status', [TicketStatus::CLOSED, TicketStatus::SOLVED])
                    ->where('tp.role', Participant::APPROVAL)
                    ->where('tp.user_id', $this->userId)
                    ->whereNull('tp.deleted_at');
                break;
            case 'observer':
                $this->builder
                    ->whereNotIn('status', [TicketStatus::CLOSED, TicketStatus::SOLVED])
                    ->where('tp.role', Participant::OBSERVER)
                    ->where('tp.user_id', $this->userId)
                    ->whereNull('tp.deleted_at');
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
