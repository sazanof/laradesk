@extends('mail.mail')
@section('content')
    <p style="font-style: italic">{!! __('export.noty.success', ['filename' => url('/user/tickets/export/' . $filename)]) !!}</p>
    <a href="{{url('/user/tickets/export/' . $filename)}}"
       target="_blank">{{url('/user/tickets/export/' . $filename)}}</a>
@endsection
