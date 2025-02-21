<?php

namespace App\Jobs;

use App\Models\News;
use App\Models\User;
use App\Notifications\NewsNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class PublishNewsArticle implements ShouldQueue
{
    use Queueable;

    public News $article;

    /**
     * Create a new job instance.
     */
    public function __construct(News $article)
    {
        $users = User::withoutTrashed();
        $this->article = $article;
        if ($this->article->only_admins) {
            $users = $users->where('is_admin', 1);
        }
        $users = $users->get();
        Notification::send($users, new NewsNotification($article));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
