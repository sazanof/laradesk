@extends('mail.mail')
@section('content')
    <p style="font-weight: bold; font-size: 18px">{{$subject}}</p>
    <p style="font-weight: bold">{{__('mail.ticket.comment.content')}}:</p>
    <p style="font-style: italic">{{$comment->content}}</p>
    <p>{{__('mail.ticket.comment.author')}}:</p>
    <ul>
        <li>{{$comment->user->firstname}} {{$comment->user->lastname}}</li>
        <li>{{$comment->user->department}}</li>
        <li>{{$comment->user->position}}</li>
        <li>{{$comment->user->email}}</li>
        <li>{{$comment->user->phone}}</li>
    </ul>
@endsection
