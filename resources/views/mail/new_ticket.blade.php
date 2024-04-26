@extends('mail.mail')
@section('content')
    <p style="font-size: 18px;text-align: center">{{$text}}</p>
    <table border="0" width="100%">
        <tr>
            <td style="padding: 6px">
                <p style="">{{__('mail.ticket.category')}}</p>
            </td>
            <td style="padding: 6px">
                {{\App\Models\Category::find($ticket->category_id)->name ?? '--'}}
            </td>
        </tr>
        <tr>
            <td style="padding: 6px">
                <p style="">{{__('mail.ticket.office')}}</p>
            </td>
            <td style="padding: 6px">
                {{$ticket?->office?->name ?? '--'}}
            </td>
        </tr>
        <tr>
            <td style="padding: 6px">
                <p style="">{{__('mail.ticket.room')}}</p>
            </td>
            <td style="padding: 6px">
                {{$ticket->room_id === -1 ? '--' : $ticket?->room?->name}} {{$ticket->custom_location}}
            </td>
        </tr>
        <tr>
            <td style="padding: 6px">
                <p style="">{{__('mail.ticket.author')}}
                    : {{$ticket->requester->firstname}} {{$ticket->requester->lastname}}</p>
            </td>
            <td style="padding: 6px">
                <p style="">{{__('mail.ticket.created')}}
                    : {{$ticket->created_at->format('d.m.Y H:i')}}</p>
            </td>
        </tr>
        @if(count($ticket->approvals) > 0)
            <tr valign="top">
                <td style="padding: 6px">{{__('mail.ticket.approvals')}}</td>
                <td style="padding: 6px">
                    @foreach($ticket->approvals as $approval)
                        <p style="margin: 0 0 4px 0">{{$approval->firstname}} {{$approval->lastname}}</p>
                    @endforeach
                </td>
            </tr>
        @endif
        @if(count($ticket->observers) > 0)
            <tr valign="top">
                <td style="padding: 6px">{{__('mail.ticket.observers')}}</td>
                <td style="padding: 6px">
                    @foreach($ticket->observers as $observer)
                        <p style="margin: 0 0 4px 0">{{$observer->firstname}} {{$observer->lastname}}</p>
                    @endforeach
                </td>
            </tr>
        @endif
    </table>
    <p style="color:#888;font-style: italic;font-size: 14px">{!! $ticket->content !!}</p>

    <p><a target="_blank" href="{{url('/#/admin/tickets/' . $ticket->id)}}">{{__('mail.ticket.url')}}</a></p>

@endsection
