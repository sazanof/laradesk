<?php

namespace App\Models;

use App\Helpdesk\Participant;
use App\Helpers\DepartmentHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Ticket
 *
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property int $department_id
 * @property int|null $office_id
 * @property int|null $room_id
 * @property string $subject
 * @property string $content
 * @property int $status
 * @property int $priority
 * @property int $need_approval
 * @property Carbon|null $solved_at
 * @property Carbon|null $closed_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $custom_location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketParticipant> $approvals
 * @property-read int|null $approvals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketParticipant> $assignees
 * @property-read int|null $assignees_count
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketFields> $fields
 * @property-read int|null $fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketFile> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketParticipant> $observers
 * @property-read int|null $observers_count
 * @property-read \App\Models\Office|null $office
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $participants
 * @property-read int|null $participants_count
 * @property-read \App\Models\User|null $requester
 * @property-read \App\Models\Room|null $room
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketThread> $thread
 * @property-read int|null $thread_count
 * @method static Builder<static>|Ticket activeDepartment()
 * @method static \Database\Factories\TicketFactory factory($count = null, $state = [])
 * @method static Builder<static>|Ticket newModelQuery()
 * @method static Builder<static>|Ticket newQuery()
 * @method static Builder<static>|Ticket onlyByRoleAndUserId(int $role, int $userId)
 * @method static Builder<static>|Ticket onlyTrashed()
 * @method static Builder<static>|Ticket query()
 * @method static Builder<static>|Ticket whereCategoryId($value)
 * @method static Builder<static>|Ticket whereClosedAt($value)
 * @method static Builder<static>|Ticket whereContent($value)
 * @method static Builder<static>|Ticket whereCreatedAt($value)
 * @method static Builder<static>|Ticket whereCustomLocation($value)
 * @method static Builder<static>|Ticket whereDeletedAt($value)
 * @method static Builder<static>|Ticket whereDepartmentId($value)
 * @method static Builder<static>|Ticket whereId($value)
 * @method static Builder<static>|Ticket whereNeedApproval($value)
 * @method static Builder<static>|Ticket whereOfficeId($value)
 * @method static Builder<static>|Ticket wherePriority($value)
 * @method static Builder<static>|Ticket whereRoomId($value)
 * @method static Builder<static>|Ticket whereSolvedAt($value)
 * @method static Builder<static>|Ticket whereStatus($value)
 * @method static Builder<static>|Ticket whereSubject($value)
 * @method static Builder<static>|Ticket whereUpdatedAt($value)
 * @method static Builder<static>|Ticket whereUserId($value)
 * @method static Builder<static>|Ticket withParticipants()
 * @method static Builder<static>|Ticket withTrashed()
 * @method static Builder<static>|Ticket withoutTrashed()
 * @mixin \Eloquent
 */
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    private $ticketUserRelationFields = [
        'ticket_participants.id',
        'ticket_participants.ticket_id',
        'ticket_participants.user_id',
        'ticket_participants.approved',
        'ticket_participants.role',
        'users.firstname',
        'users.lastname',
        'users.email',
        'users.position',
        'users.phone',
        'users.department',
        'users.organization',
        'users.photo',
        'users.office_id',
        'users.room_id',
    ];

    protected $fillable = [
        'user_id',
        'category_id',
        'department_id',
        'custom_location',
        'office_id',
        'room_id',
        'subject',
        'content',
        'status',
        'priority',
        'need_approval',
        'solved_at',
        'closed_at'
    ];


    /**
     * @param Builder $query
     * @return void
     */
    public function scopeActiveDepartment(Builder $query)
    {
        /** @var User $user */
        $user = Auth::user();
        $d = DepartmentHelper::getDepartment($user);
        if (!is_null($d)) {
            $query->where('tickets.department_id', '=', $d);
        }
    }


    public function scopeWithParticipants(Builder $query)
    {
        $query->select(['tickets.id'])
            ->selectRaw('tp.ticket_id as tp_ticket_id,tp.role as tp_role, tp.user_id as tp_user_id')
            ->join('ticket_participants as tp', 'tickets.id', 'tp.ticket_id')
            ->whereNull('tp.deleted_at');
    }

    /**
     * @param Builder $query
     * @param int $role
     * @param int $userId
     * @return Builder
     */
    public function scopeOnlyByRoleAndUserId(Builder $query, int $role, int $userId)
    {
        return $query
            ->where('tp.role', $role)
            ->where('tp.user_id', $userId);
    }

    /**
     * @return HasOne
     */
    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id')->select('name', 'id');
    }

    /**
     * @return HasOne
     */
    public function category()
    {
        return $this
            ->hasOne(Category::class, 'id', 'category_id')
            ->select([
                'id',
                'name',
                'department_id',
                'description'
            ]);
    }

    /**
     * @return HasMany
     */
    public function fields()
    {
        return $this
            ->hasMany(TicketFields::class, 'ticket_id', 'id')
            ->join('fields', 'fields.id', '=', 'ticket_fields.field_id')
            ->select(['ticket_fields.id', 'ticket_fields.ticket_id', 'ticket_fields.content'])
            ->selectRaw('fields.name as field_name, fields.id as field_id, fields.type as field_type');
    }

    /**
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(TicketFile::class, 'ticket_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function requester(): HasOne
    {
        return $this
            ->hasOne(User::class, 'id', 'user_id')
            ->select([
                'id',
                'room_id',
                'office_id',
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
            ->hasMany(TicketParticipant::class, 'ticket_id', 'id')
            ->where('role', Participant::ASSIGNEE)
            ->join('users', 'users.id', 'ticket_participants.user_id')
            ->select($this->ticketUserRelationFields);
    }

    public function approvals()
    {
        return $this
            ->hasMany(TicketParticipant::class, 'ticket_id', 'id')
            ->where('role', Participant::APPROVAL)
            ->join('users', 'users.id', 'ticket_participants.user_id')
            ->select($this->ticketUserRelationFields);
    }

    public function observers()
    {
        return $this
            ->hasMany(TicketParticipant::class, 'ticket_id', 'id')
            ->where('role', Participant::OBSERVER)
            ->join('users', 'users.id', 'ticket_participants.user_id')
            ->select($this->ticketUserRelationFields);
    }

    public function participants()
    {
        return $this
            ->hasManyThrough(
                User::class,
                TicketParticipant::class,
                'ticket_id',
                'id',
                'id',
                'user_id' //on `ticket_participants`.`user_id`
            )
            ->select(['users.*', 'ticket_participants.role']);
    }

    /**
     * @return HasMany
     */
    public function thread()
    {
        return $this->hasMany(TicketThread::class, 'ticket_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');
    }

}
