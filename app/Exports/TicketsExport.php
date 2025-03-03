<?php

namespace App\Exports;

use App\Helpdesk\TicketStatus;
use App\Models\Category;
use App\Models\Department;
use App\Models\Office;
use App\Models\Ticket;
use App\Models\TicketParticipant;
use App\Models\TicketThread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    protected Collection $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->collection;

    }

    public function headings(): array
    {
        return [
            __('export.number'),
            __('export.department'),
            __('export.category'),
            __('export.subject'),
            __('export.content'),
            __('export.requester'),
            __('export.assignees'),
            __('export.approvals'),
            __('export.observers'),
            __('export.date'),
            __('export.closed_at'),
            __('export.solved_at'),
            __('export.office'),
            __('export.room'),
            __('export.status'),
            __('export.comments_count'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 27,
            'B' => 33,
            'C' => 33,
            'D' => 50,
            'E' => 33,
            'F' => 33,
            'G' => 50,
            'H' => 60,
            'I' => 60,
            'J' => 60,
            'K' => 60,
            'L' => 60,
            'M' => 50,
            'N' => 50,
            'O' => 33,
            'P' => 20,
        ];
    }

    /**
     * @var Ticket $ticket
     */
    public function map($ticket): array
    {
        return [
            Str::padLeft($ticket->id, 10, '0'),
            Department::find($ticket->department_id)->name,
            $this->mapCategory($ticket->category_id),
            $ticket->subject,
            strip_tags($ticket->content),
            $this->mapUser(User::withTrashed()->find($ticket->user_id)),
            $this->mapUsers($ticket->assignees()->withTrashed()->get()),
            $this->mapUsers($ticket->approvals()->withTrashed()->get()),
            $this->mapUsers($ticket->observers()->withTrashed()->get()),
            $ticket->created_at?->format('d.m.Y H:i'),
            $ticket->closed_at !== null ? Carbon::parse($ticket->closed_at)->format('d.m.Y H:i') : '',
            $ticket->solved_at !== null ? Carbon::parse($ticket->solved_at)->format('d.m.Y H:i') : '',
            $this->mapOffice($ticket->office_id),
            $ticket->room_id,
            $this->mapStatus($ticket->status),
            TicketThread::where('ticket_id', $ticket->id)->count()

        ];
    }

    protected function mapOffice($id)
    {
        $o = Office::find($id);
        return !is_null($o) ? $o->name : '';
    }

    protected function mapStatus($status)
    {
        return __('export.status_' . $status);
    }

    protected function mapCategory(int $categoryId)
    {
        $cat = Category::find($categoryId);
        $names = $names ?? [];
        $names[] = $cat->name;
        if ($cat->parent > 0) {
            $parentCat = Category::find($cat->parent);
            if (!empty($parentCat)) {
                $this->mapCategory($parentCat->id);
            }
        }
        return implode(' > ', array_reverse($names));
    }

    protected function mapUser(User|TicketParticipant $user)
    {
        return "$user->firstname $user->lastname";
    }

    protected function mapUsers(Collection $users)
    {
        /** @var User $user */
        $u = [];
        foreach ($users as $user) {
            $u[] = $this->mapUser($user);
        }
        return implode(', ', $u);
    }
}
