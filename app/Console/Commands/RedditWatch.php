<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Watch;
use Carbon\Carbon;
use Artisan;

class RedditWatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reddit:watch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'watch the thread';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $watches = Watch::where('created_at', '>=', Carbon::parse('4 hours ago')->toDatetimeString())
            ->get()
            ->unique('reddit_id');

        if ($watches->count() < 1) {
            $this->info("\nnothing to do\n");
            exit;
        }

        $bar = $this->output->createProgressBar($watches->count());

        foreach ($watches as $watch) {
            Artisan::queue('reddit:comment', [
                'subreddit' => $watch->subreddit,
                'thread'    => $watch->reddit_id,
            ]);

            $bar->advance();
        }

        $bar->finish();

        $this->info("\n");
    }
}
