@extends('mail.mail')
@section('content')
    <p style="font-weight: bold;text-align: center">{{$ticket->subject}}</p>
    <p style="font-weight: bold">{{__('mail.ticket.comment.content')}}:</p>
    <p style="font-style: italic;color:#526b80">{{$comment->content}}</p>
    <p>{{__('mail.ticket.comment.author')}}:</p>
    <ul>
        <li>{{$comment->user->firstname}} {{$comment->user->lastname}}</li>
        <li>{{$comment->user->department}}</li>
        <li>{{$comment->user->position}}</li>
        <li>{{$comment->user->email}}</li>
        <li>{{$comment->user->phone}}</li>
    </ul>
@endsection
