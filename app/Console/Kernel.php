<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sync users from ldap
        // $schedule->command('inspire')->hourly();
        $filter = sprintf("(|(memberOf=%s))", config('hd.ldap.users.root_group'));
        $schedule->command('ldap:import users', ['--no-interaction', '--filter' => $filter, '--delete', '--restore'])
            ->hourly();

        // Delete old notifications
        // https://stackoverflow.com/questions/51195673/make-scheduler-to-delete-notifications-that-are-15-days-old-from-now-in-laravel
        $schedule->call(function () {

            $now = Carbon::now();

            DB::table('notifications')
                ->where('created_at', '<', $now->subDays(30))
                ->delete();
        })->daily();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
