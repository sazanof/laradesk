<?php

namespace App\Helpers;

use App\Helpdesk\TicketParticipant;
use App\Helpdesk\TicketStatus;
use App\Models\Department;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChartHelper
{
    protected ?array $datasets = [];
    protected ?array $labels = [];
    protected ?Carbon $start = null;
    protected ?Carbon $end = null;
    protected Builder $builder;

    public function __construct()
    {
        $this->builder = DB::query();
    }

    /**
     * @param Builder $builder
     */
    public function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @return array
     */
    public function getDatasets(): array
    {
        return $this->datasets;
    }

    /**
     * @return array
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @param array|null $datasets
     */
    public function setDatasets(?array $datasets): void
    {
        $this->datasets = is_null($datasets) ? [] : $datasets;
    }

    /**
     * @param array|null $labels
     */
    public function setLabels(?array $labels): void
    {
        $this->labels = is_null($labels) ? [] : $labels;
    }

    /**
     * @param Carbon|null $start
     */
    public function setStart(?Carbon $start): void
    {
        $this->start = $start;
    }

    /**
     * @param Carbon|null $end
     */
    public function setEnd(?Carbon $end): void
    {
        $this->end = $end;
    }

    /**
     * @return Carbon|null
     */
    public function getEnd(): ?Carbon
    {
        return $this->end;
    }

    /**
     * @return Carbon|null
     */
    public function getStart(): ?Carbon
    {
        return $this->start;
    }

    function stringToColorCode($str, int $opacity = 50): string
    {
        $code = dechex(crc32($str));
        return '#' . substr($code, 0, 6) . $opacity;
    }

    public function searchBetweenDates(string $column): void
    {
        $this->builder->whereBetween($column, [$this->start, $this->end]);
    }


    /**
     * @param array|null $departmentIds
     * @return Collection
     */
    public function queryTicketsCountByStatusAndDepartments(?array $departmentIds = null): Collection
    {
        $empty = [
            'label' => null,
            'backgroundColor' => null,
            'data' => [
                TicketStatus::NEW => 0,
                TicketStatus::IN_WORK => 0,
                TicketStatus::WAITING => 0,
                TicketStatus::SOLVED => 0,
                TicketStatus::CLOSED => 0,
                TicketStatus::IN_APPROVAL => 0,
                TicketStatus::APPROVED => 0,
            ]
        ];
        $labels = [
            __('export.status_' . TicketStatus::NEW),
            __('export.status_' . TicketStatus::IN_WORK),
            __('export.status_' . TicketStatus::WAITING),
            __('export.status_' . TicketStatus::SOLVED),
            __('export.status_' . TicketStatus::CLOSED),
            __('export.status_' . TicketStatus::IN_APPROVAL),
            __('export.status_' . TicketStatus::APPROVED),
        ];

        $datasets = [];
        $this->setLabels($labels);

        $this->builder->selectRaw('d.id, d.name,  t.department_id,  t.status,count(t.status) as total')
            ->from('tickets', 't')
            ->join('departments as d', 't.department_id', 'd.id');
        if (!empty($departmentIds)) {
            $this->builder->whereIn('d.id', $departmentIds);
        }
        $this->builder->groupBy('t.department_id')->groupBy('t.status');
        $this->builder->whereNull('t.deleted_at');
        // dump($this->builder->get()->toArray());
        $ar = $this->builder->count() > 0 ? $this->builder->get()->toArray() : [];

        foreach ($ar as $item) {
            if (!array_key_exists($item->id, $datasets)) {
                $datasets[$item->id] = array_merge($empty, [
                    'label' => $item->name,
                    'borderColor' => $this->stringToColorCode($item->name, 80),
                    'backgroundColor' => $this->stringToColorCode($item->name),
                    'borderWidth' => 1,
                ]);
            }
            $datasets[$item->id]['data'][$item->status] = $item->total;
        }

        foreach ($datasets as $key => $item) {
            $datasets[$key]['data'] = array_values($item['data']);
        }

        $this->setDatasets($datasets);
        return $this->builder->get();
    }

    /**
     * @param Department $department
     * @return Collection
     */
    public function queryTicketsCountByUserAndDepartment(Department $department): Collection
    {
        $empty = [
            'label' => null,
            'backgroundColor' => null,
            'data' => []
        ];


        $labels = [
            __('export.status_' . TicketStatus::NEW),
            __('export.status_' . TicketStatus::IN_WORK),
            __('export.status_' . TicketStatus::WAITING),
            __('export.status_' . TicketStatus::SOLVED),
            __('export.status_' . TicketStatus::CLOSED),
            __('export.status_' . TicketStatus::IN_APPROVAL),
            __('export.status_' . TicketStatus::APPROVED),
        ];


        $datasets = [];
        $this->setLabels($labels);
        //select u.firstname, u.lastname, count(t.id) as total_tickets, t.status
        //from tickets as t
        //join ticket_participants as p on t.id = p.ticket_id
        //join users u on u.id = p.user_id
        //where t.department_id=2 and p.role = 2 # ASSIGNEE
        //group by t.status
        //order by t.status asc
        $this->builder
            ->selectRaw('u.id, u.firstname, u.lastname, t.status, count(t.id) as total')
            ->from('tickets', 't')
            ->join('ticket_participants as p', 't.id', 'p.ticket_id')
            ->join('users as u', 'u.id', 'p.user_id');

        $this->builder->where('t.department_id', $department->id);
        $this->builder->where('p.role', TicketParticipant::ASSIGNEE);
        $this->builder
            ->groupBy('t.status')
            ->groupBy('u.id');
        $this->builder->whereNull('t.deleted_at');
        $this->builder->orderBy('t.status');

        // dump($this->builder->get()->toArray());
        $ar = $this->builder->count() > 0 ? $this->builder->get()->toArray() : [];

        foreach ($ar as $item) {
            if (!array_key_exists($item->id, $datasets)) {
                $fullname = $item->lastname . ' ' . $item->firstname;
                $datasets[$item->id] = array_merge($empty, [
                    'label' => $fullname,
                    'borderColor' => $this->stringToColorCode($fullname, 80),
                    'backgroundColor' => $this->stringToColorCode($fullname),
                    'borderWidth' => 1,
                ]);
            }
            $datasets[$item->id]['data'][$item->status] = $item->total;
        }

        foreach ($datasets as $key => $item) {
            $datasets[$key]['data'] = array_values($item['data']);
        }

        $this->setDatasets($datasets);
        return $this->builder->get();
    }

    public function toChartData()
    {
        return [
            'labels' => $this->getLabels(),
            'datasets' => array_values($this->getDatasets())
        ];
    }
}
