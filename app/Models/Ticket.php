<?php

namespace App\Models;

use App\Helpdesk\TicketParticipant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * \App\Models\Ticket
 *
 * @property int $id
 * @property int $number
 * @property int $user_id
 * @property int $category_id
 * @property string $subject
 * @property string $content
 * @property int $status
 * @property int $priority
 * @property int $need_approval
 * @property string|null $solved_at
 * @property string|null $closed_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Ticket newModelQuery()
 * @method static Builder|Ticket newQuery()
 * @method static Builder|Ticket onlyTrashed()
 * @method static Builder|Ticket query()
 * @method static Builder|Ticket whereCategoryId($value)
 * @method static Builder|Ticket whereClosedAt($value)
 * @method static Builder|Ticket whereContent($value)
 * @method static Builder|Ticket whereCreatedAt($value)
 * @method static Builder|Ticket whereDeletedAt($value)
 * @method static Builder|Ticket whereId($value)
 * @method static Builder|Ticket whereNeedApproval($value)
 * @method static Builder|Ticket whereNumber($value)
 * @method static Builder|Ticket wherePriority($value)
 * @method static Builder|Ticket whereSolvedAt($value)
 * @method static Builder|Ticket whereStatus($value)
 * @method static Builder|Ticket whereSubject($value)
 * @method static Builder|Ticket whereUpdatedAt($value)
 * @method static Builder|Ticket whereUserId($value)
 * @method static Builder|Ticket withTrashed()
 * @method static Builder|Ticket withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketParticipants> $approvals
 * @property-read int|null $approvals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketParticipants> $assignees
 * @property-read int|null $assignees_count
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketFields> $fields
 * @property-read int|null $fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketParticipants> $observers
 * @property-read int|null $observers_count
 * @property-read \App\Models\User|null $requester
 * @method static \Database\Factories\TicketFactory factory($count = null, $state = [])
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketParticipants> $approvals
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketParticipants> $assignees
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketFields> $fields
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketParticipants> $observers
 * @mixin \Eloquent
 */
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    private $ticketUserRelationFields = [
        'ticket_participants.ticket_id',
        'ticket_participants.user_id',
        'ticket_participants.approved',
        'users.firstname',
        'users.lastname',
        'users.email',
        'users.position',
        'users.phone',
        'users.department',
        'users.organization',
        'users.photo'
    ];

    protected $fillable = [
        'user_id',
        'category_id',
        'subject',
        'content',
        'status',
        'priority',
        'need_approval',
        'solved_at',
        'closed_at'
    ];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id')->select(['id', 'name', 'description']);
    }

    public function fields()
    {
        return $this
            ->hasMany(TicketFields::class, 'ticket_id', 'id')
            ->join('fields', 'fields.id', '=', 'ticket_fields.field_id')
            ->select(['ticket_fields.id', 'ticket_fields.ticket_id', 'ticket_fields.content'])
            ->selectRaw('fields.name as field_name, fields.id as field_id, fields.type as field_type');
    }

    public function requester()
    {
        return $this
            ->hasOne(User::class, 'id', 'user_id')
            ->select([
                'id',
                'firstname',
                'lastname',
                'email',
                'position',
                'phone',
                'department',
                'organization',
                'photo']);
    }

    public function assignees()
    {
        return $this
            ->hasMany(TicketParticipants::class, 'ticket_id', 'id')
            ->where('role', TicketParticipant::ASSIGNEE)
            ->join('users', 'users.id', 'ticket_participants.user_id')
            ->select($this->ticketUserRelationFields);
    }

    public function approvals()
    {
        return $this
            ->hasMany(TicketParticipants::class, 'ticket_id', 'id')
            ->where('role', TicketParticipant::APPROVAL)
            ->join('users', 'users.id', 'ticket_participants.user_id')
            ->select($this->ticketUserRelationFields);
    }

    public function observers()
    {
        return $this
            ->hasMany(TicketParticipants::class, 'ticket_id', 'id')
            ->where('role', TicketParticipant::OBSERVER)
            ->join('users', 'users.id', 'ticket_participants.user_id')
            ->select($this->ticketUserRelationFields);
    }


}
