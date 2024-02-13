@if(!is_null($user))
    <div class="participant">
        <div class="name">{{$user->firstname}} {{$user->lastname}}</div>
        <div class="position">{{$user->department}}, {{$user->position}}</div>
        <div class="position">{{$user->email}}</div>
        <div class="position">{{$user->phone}}</div>
    </div>

@endif
