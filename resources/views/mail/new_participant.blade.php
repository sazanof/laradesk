@extends('mail.mail')
@section('content')
    <p style="font-weight: bold">{{$ticket->subject}}</p>
    <p>{{__('mail.ticket.participant.new')}}</p>
    @if($participant->role===\App\Helpdesk\TicketParticipant::REQUESTER)
        {{__('mail.ticket.role.text')}}: {{__('mail.ticket.role.requester')}}
    @elseif($participant->role===\App\Helpdesk\TicketParticipant::ASSIGNEE)
        {{__('mail.ticket.role.text')}}: {{__('mail.ticket.role.assignee')}}
    @elseif($participant->role===\App\Helpdesk\TicketParticipant::APPROVAL)
        {{__('mail.ticket.role.text')}}: {{__('mail.ticket.role.approval')}}
    @elseif($participant->role===\App\Helpdesk\TicketParticipant::OBSERVER)
        {{__('mail.ticket.role.text')}}: {{__('mail.ticket.role.observer')}}
    @endif

    <ul>
        <li>{{$participant->firstname}} {{$participant->lastname}}</li>
        <li>{{$participant->department}}</li>
        <li>{{$participant->position}}</li>
        <li>{{$participant->email}}</li>
        <li>{{$participant->phone}}</li>

    </ul>
@endsection
