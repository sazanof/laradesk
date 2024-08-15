<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\User;
use App\Notifications\NewsNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class NewsController extends Controller
{

    public function getUserNews(Request $request): Collection
    {
        /** @var User $user */
        $user = $request->user();
        return $user
            ->notifications()
            ->where('read_at', null)
            ->where('type', 'App\Notifications\NewsNotification')
            ->get();
    }

    public function getNews(int $page = 1): LengthAwarePaginator
    {
        return News::paginate(perPage: 25, page: $page);
    }

    public function getLatestNews(): array|Collection|\Illuminate\Support\Collection
    {
        return News::limit(10)->get();
    }

    public function addNew(Request $request): Model|News
    {
        $title = $request->input('title');
        $text = $request->input('text');

        return News::create([
            'title' => $title,
            'text' => $text
        ]);
    }

    public function updateNew(Request $request, int $id): bool|int
    {
        $title = $request->input('title');
        $text = $request->input('text');
        return News::where('id', $id)->update([
            'title' => $title,
            'text' => $text
        ]);
    }

    public function deleteNew(int $id)
    {
        $new = News::find($id);
        if (!$new) {
            return false;
        }
        $this->newsNotifications($new);
        return News::where('id', $id)->delete();
    }

    public function publishNew(int $id)
    {
        $new = News::find($id);
        if (!$new) {
            return false;
        }
        $users = User::withoutTrashed()->get();
        Notification::send($users, new NewsNotification($new));
        $new->published = true;
        $new->save();
        return \request()->json(['success' => true, 'count' => $users->count()]);
    }

    public function unPublishNew(int $id)
    {
        $new = News::find($id);
        $new->published = false;
        $new->save();
        return $this->newsNotifications($new)->delete();
    }

    public function readNew(string $id, Request $request): int
    {
        /** @var User $user */
        $user = $request->user();
        $user
            ->unreadNotifications()
            ->where('id', $id)->first()->markAsRead();
        return true;
    }

    protected function newsNotifications(News $new, bool $unreadOnly = false): Builder
    {
        $q = DB::table('notifications')
            ->where('type', 'App\Notifications\NewsNotification')
            ->where('notifiable_type', 'App\Models\User')
            ->whereRaw("JSON_EXTRACT(`data`, '$.id') = ?", [$new->id]);
        if ($unreadOnly) {
            $q = $q->whereNull('read_at');
        }
        return $q;
    }
}
