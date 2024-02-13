@extends('export.pdf')
@section('content')
    <div class="ticket">
        <div class="ticket-info">
            <div class="status status_{{$ticket->status}}">
                {{__('pdf.status')}}: {{__('export.status_' . $ticket->status)}}
            </div>
            <div class="date">
                {{__('pdf.created_at')}}
                : {{\Illuminate\Support\Carbon::parse($ticket->created_at)->format('d.m.Y H:i')}}
            </div>
        </div>

        <h1 class="title">#{{\Illuminate\Support\Str::padLeft($ticket->id, 10, '0')}} {{$ticket->subject}}</h1>
        <div class="description">{{$ticket->content}}</div>
        @include('export.chunks.fields',['fields' => $ticket->fields])
        <div class="participants-block">
            <div class="subtitle">{{__('pdf.requester')}}</div>
            @include('export.chunks.participant',['user'=>$ticket->requester])
        </div>

        @if($ticket->observers != null && count($ticket->observers) > 0)
            <div class="participants-block">
                <div class="subtitle">{{__('pdf.observers')}}</div>
                <div class="participants">
                    @foreach($ticket->observers as $observer)
                        @include('export.chunks.participant',['user'=>$observer])
                    @endforeach
                </div>

            </div>
        @endif

        @if($ticket->approvals != null && count($ticket->approvals) > 0)
            <div class="participants-block">
                <div class="subtitle">{{__('pdf.approvals')}}</div>
                <div class="participants">
                    @foreach($ticket->approvals as $approval)
                        @include('export.chunks.participant',['user'=>$approval])
                    @endforeach
                </div>
            </div>
        @endif

        @if($ticket->assignees != null && count($ticket->assignees) > 0)
            <div class="participants-block">
                <div class="subtitle">{{__('pdf.assignees')}}</div>
                <div class="participants">
                    @foreach($ticket->assignees as $assignee)
                        @include('export.chunks.participant',['user'=>$assignee])
                    @endforeach
                </div>
            </div>
        @endif

        @if($ticket->thread != null && count($ticket->thread) > 0)
            <div class="thread">
                @foreach($ticket->thread as $comment)
                    @include('export.chunks.thread_item',['comment'=>$comment])
                @endforeach
            </div>
        @endif
    </div>

@endsection
