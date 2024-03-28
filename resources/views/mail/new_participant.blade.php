@extends('mail.mail')
@section('content')
    <p style="font-weight: bold">{{$text}}</p>

    @if($participant->role===\App\Helpdesk\Participant::REQUESTER)
        {{__('mail.ticket.role.text')}}: {{__('mail.ticket.role.requester')}}
    @elseif($participant->role===\App\Helpdesk\Participant::ASSIGNEE)
        {{__('mail.ticket.role.text')}}: {{__('mail.ticket.role.assignee')}}
    @elseif($participant->role===\App\Helpdesk\Participant::APPROVAL)
        {{__('mail.ticket.role.text')}}: {{__('mail.ticket.role.approval')}}
    @elseif($participant->role===\App\Helpdesk\Participant::OBSERVER)
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
