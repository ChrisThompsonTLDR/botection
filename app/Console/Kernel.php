<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\OauthRefresh::class,
        \App\Console\Commands\RedditWatch::class,
        \App\Console\Commands\RedditThread::class,
        \App\Console\Commands\RedditComment::class,
        \App\Console\Commands\RedditAuthor::class,
        \App\Console\Commands\AuthorResponse::class,
        \App\Console\Commands\TreeRebuild::class,
        \App\Console\Commands\RedditSubreddit::class,
        \App\Console\Commands\RedditSubreddits::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('oauth:refresh')->everyFifteenMinutes();

        $schedule->command('horizon:snapshot')->everyFiveMinutes();

//        $schedule->command('reddit:watch')->everyFiveMinutes();

        $schedule->command('reddit:subreddit', ['JoeRogan'])->everyMinute();
//        $schedule->command('reddit:subreddit', ['MMA'])->everyMinute();
//        $schedule->command('reddit:subreddit', ['ChapoTrapHouse'])->everyMinute();
//        $schedule->command('reddit:subreddit', ['samharris'])->everyMinute();
//        $schedule->command('reddit:subreddit', ['JordanPeterson'])->everyMinute();
//        $schedule->command('reddit:subreddit', ['thefighterandthekid'])->everyMinute();
//        $schedule->command('reddit:subreddit', ['Ice_Poseidon'])->everyMinute();
//        $schedule->command('reddit:subreddit', ['milliondollarextreme'])->everyMinute();
//        $schedule->command('reddit:subreddit', ['The_Donald'])->everyMinute();

        $schedule->command('reddit:subreddits')->everyMinute();

        $schedule->command('author:response')->everyFifteenMinutes();

        $schedule->command('reddit:comment')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
