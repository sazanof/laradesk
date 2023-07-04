@extends('mail.mail')
@section('content')
    @if($participant->user_id === \Illuminate\Support\Facades\Auth::id())
        <p style="text-align:center;color:green;font-weight: bold"> {{__('mail.ticket.participant.you_added_to_ticket',['id'=>$ticket->id])}}</p>
    @else
        <p>{{__('mail.ticket.participant.new')}}</p>
    @endif
    <p style="font-weight: bold">{{$ticket->subject}}</p>
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
        <li>{{$participant->firstname ?? $participant->user->firstname}} {{$participant->lastname ?? $participant->user->lastname}}</li>
        <li>{{$participant->department ?? $participant->user->department}}</li>
        <li>{{$participant->position ?? $participant->user->position}}</li>
        <li>{{$participant->email ?? $participant->user->email}}</li>
        <li>{{$participant->phone ?? $participant->user->phone}}</li>
    </ul>
@endsection
