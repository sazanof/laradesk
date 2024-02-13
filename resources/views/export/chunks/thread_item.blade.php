<div class="comment">
    <div class="comment-date">{{\Illuminate\Support\Carbon::parse($comment->created_at)->format('d.m.Y H:i')}}</div>
    <div class="comment-author">
        <div class="comment-author__name">
            {{$comment->user->firstname}} {{$comment->user->lastname}}
        </div>
    </div>
    <div class="comment-content">
        {{$comment->content}}
    </div>
</div>
