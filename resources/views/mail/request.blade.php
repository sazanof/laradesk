@extends('mail.mail')
@section('content')
    <p>{{__('mail.request.text', ['firstname'=>$user->firstname,'lastname'=>$user->lastname])}}</p>
    <p>{{__('mail.request.message_text')}}</p>
    <p>{{$messageText}}</p>
@endsection
