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
    protected array $options = [];

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
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function addOption(string $key, mixed $value)
    {
        $this->options[$key] = $value;
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
     * @param string $label
     * @return void
     */
    public function addLabel(string $label): void
    {
        if (!in_array($label, $this->labels)) {
            $this->labels[] = $label;
        }
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
            ]
        ];
        $datasets = [];


        $this->builder->selectRaw('d.id, d.name,  t.status,count(t.status) as total')
            ->from('tickets', 't')
            ->join('departments as d', 't.department_id', 'd.id');
        if (!empty($departmentIds)) {
            $this->builder->whereIn('d.id', $departmentIds);
        }
        $this->builder->groupBy('t.department_id')->groupBy('t.status');
        $this->builder->whereNull('t.deleted_at');
        $ar = $this->builder->count() > 0 ? $this->builder->get()->toArray() : [];
        foreach ($ar as $item) {
            $this->addLabel($item->name);
            if (!array_key_exists($item->status, $datasets)) {
                $datasets[$item->status] = array_merge($empty, [
                    'label' => __('export.status_' . $item->status),
                    'borderColor' => $this->stringToColorCode(__('export.status_' . $item->status), 80),
                    'backgroundColor' => $this->stringToColorCode(__('export.status_' . $item->status)),
                    'borderWidth' => 1,
                ]);
            }
            $datasets[$item->status]['data'][] = $item->total;
        }

        foreach ($datasets as $key => $item) {
            $datasets[$key]['data'] = array_values($item['data']);
        }
        //$this->addOption('indexAxis', 'y');
        // $this->setLabels($labels);
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
            $this->addLabel($item->lastname . ' ' . $item->firstname);
            if (!array_key_exists($item->status, $datasets)) {
                $datasets[$item->status] = array_merge($empty, [
                    'label' => __('export.status_' . $item->status),
                    'borderColor' => $this->stringToColorCode(__('export.status_' . $item->status), 80),
                    'backgroundColor' => $this->stringToColorCode(__('export.status_' . $item->status)),
                    'borderWidth' => 1,
                ]);
            }
            $datasets[$item->status]['data'][] = $item->total;
        }

        foreach ($datasets as $key => $item) {
            $datasets[$key]['data'] = array_values($item['data']);
        }
        $this->addOption('indexAxis', 'y');
        //$this->setLabels($labels);
        $this->setDatasets($datasets);
        return $this->builder->get();
    }

    /**
     * @param Department $department
     * @return Collection
     */
    public function queryTicketsCountByCategoryAndDepartment(Department $department): Collection
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

        $this->builder
            ->selectRaw('c.id, c.name, c.department_id, count(t.id) as total, t.status')
            ->from('categories', 'c')
            ->join('tickets as t', 'c.id', 't.category_id');

        $this->builder->where(function (Builder $builder) use ($department) {
            return $builder
                ->where('c.department_id', $department->id)
                ->orWhereNull('c.department_id');
        });
        //  $this->builder->where('c.department_id', $department->id);
        $this->builder
            ->groupBy('t.category_id')
            ->groupBy('t.status');
        $this->builder->whereNull('t.deleted_at');
        $this->builder->orderBy('t.status');
        $ar = $this->builder->count() > 0 ? $this->builder->get()->toArray() : [];
        // dd($ar);
        $i = 0;
        foreach ($ar as $item) {
            $this->addLabel($item->name);
            if (!array_key_exists($item->status, $datasets)) {
                $datasets[$item->status] = array_merge($empty, [
                    'label' => __('export.status_' . $item->status),
                    'borderColor' => $this->stringToColorCode(__('export.status_' . $item->status), 80),
                    'backgroundColor' => $this->stringToColorCode(__('export.status_' . $item->status)),
                    'borderWidth' => 1,
                    'status' => $item->status,
                    'data' => [],
                ]);
            }
            $datasets[$item->status]['data'][] = $item->total;
            $i++;
        }
        // dd($this->labels, $datasets);
        //dd($labels);
        foreach ($datasets as $key => $item) {
            $datasets[$key]['data'] = array_values($item['data']);
        }
        /*$this->addOption('indexAxis', 'y');
        $this->addOption('scales', [
            'y' => [
                'stacked' => true
            ],
            'x' => [
                'stacked' => true
            ]
        ]);*/
        // $this->setLabels($labels);
        $this->setDatasets($datasets);
        return $this->builder->get();
    }

    public function toChartData()
    {
        return [
            'options' => $this->options,
            'data' => [
                'labels' => $this->getLabels(),
                'datasets' => array_values($this->getDatasets())
            ]
        ];
    }
}
