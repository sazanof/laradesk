<?php

namespace App\Helpers;

use App\Helpdesk\Participant;
use App\Helpdesk\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TicketsCounterHelper
{
    protected ?User $user = null;

    protected static ?TicketsCounterHelper $helper = null;

    protected ?int $departmentId = null;

    public function __construct(User $user = null)
    {
        $this->user = $user === null ? Auth::user() : $user;
        $this->departmentId = DepartmentHelper::getDepartment($this->user);
        self::$helper = $this;
    }

    protected static function getInstance()
    {
        if (is_null(self::$helper)) {
            return new self();
        }
        return self::$helper;
    }

    protected static function addDepartmentToQuery(Builder $builder)
    {
        if (!is_null(self::$helper->departmentId)) {
            $builder->where('department_id', self::$helper->departmentId);
        }
    }

    public static function adminNew()
    {
        self::getInstance();
        $tickets = Ticket::query()
            ->select()
            ->whereIn('status', [TicketStatus::NEW, TicketStatus::IN_APPROVAL]);
        self::addDepartmentToQuery($tickets);
        return $tickets->count();

    }

    public static function adminAll()
    {
        self::getInstance();
        $tickets = Ticket::query()
            ->select();
        self::addDepartmentToQuery($tickets);
        return $tickets->count();
    }

    public static function adminWaiting()
    {
        self::getInstance();
        $tickets = Ticket::query()
            ->select()
            ->where('status', TicketStatus::WAITING);
        self::addDepartmentToQuery($tickets);
        return $tickets->count();
    }

    public static function adminInWork()
    {
        self::getInstance();
        $tickets = Ticket::query()
            ->select()
            ->where('status', TicketStatus::IN_WORK);
        self::addDepartmentToQuery($tickets);
        return $tickets->count();
    }

    public static function adminClosed()
    {
        self::getInstance();
        $tickets = Ticket::query()
            ->select()
            ->where('status', TicketStatus::CLOSED);
        self::addDepartmentToQuery($tickets);
        return $tickets->count();
    }

    public static function adminSolved()
    {
        self::getInstance();
        $tickets = Ticket::query()
            ->select()
            ->where('status', TicketStatus::SOLVED);
        self::addDepartmentToQuery($tickets);
        return $tickets->count();
    }

    public static function adminIsAssignee()
    {
        self::getInstance();
        $tickets = Ticket::query()
            ->select()
            ->withParticipants()
            ->onlyByRoleAndUserId(Participant::ASSIGNEE, self::$helper->user->id);
        self::addDepartmentToQuery($tickets);

        return $tickets->count();
    }

    public static function adminOnApproval()
    {
        self::getInstance();
        $tickets = Ticket::query()
            ->select()
            ->where('status', TicketStatus::IN_APPROVAL);
        self::addDepartmentToQuery($tickets);
        return $tickets->count();
    }

    public static function userIsApproval()
    {
        self::getInstance();
        return Ticket::query()
            ->select()
            ->withParticipants()
            ->onlyByRoleAndUserId(Participant::APPROVAL, self::$helper->user->id)
            ->count();
    }

    public static function userIsObserver()
    {
        self::getInstance();
        return Ticket::query()
            ->select()
            ->withParticipants()
            ->onlyByRoleAndUserId(Participant::OBSERVER, self::$helper->user->id)
            ->count();
    }

    public static function sentAll()
    {
        self::getInstance();
        return Ticket::query()
            ->select()->where('user_id', self::$helper->user->id)->count();
    }

    public static function sentWaiting()
    {
        self::getInstance();
        return Ticket::query()
            ->select()
            ->where('user_id', self::$helper->user->id)
            ->where('status', TicketStatus::WAITING)
            ->count();
    }

    public static function sentClosed()
    {
        self::getInstance();
        return Ticket::query()
            ->select()
            ->where('user_id', self::$helper->user->id)
            ->where('status', TicketStatus::CLOSED)
            ->count();
    }

    public static function sentInWork()
    {
        self::getInstance();
        return Ticket::query()
            ->select()
            ->where('user_id', self::$helper->user->id)
            ->where('status', TicketStatus::IN_WORK)
            ->count();
    }

    public static function sentOnApprooval()
    {
        self::getInstance();
        return Ticket::query()
            ->select()
            ->where('user_id', self::$helper->user->id)
            ->where('status', TicketStatus::IN_APPROVAL)
            ->count();
    }

    public static function sentSolved()
    {
        self::getInstance();
        return Ticket::query()
            ->select()
            ->where('user_id', self::$helper->user->id)
            ->where('status', TicketStatus::SOLVED)
            ->count();
    }
}
