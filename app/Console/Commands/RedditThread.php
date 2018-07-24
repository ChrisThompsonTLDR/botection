<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Log;
use App\Jobs\ProcessThread;

class RedditThread extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reddit:thread {thread}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pull a thread';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        if (!($threads = Cache::get('subreddit:' . $this->argument('subreddit')))) {
            $pushshift = new Client([
                'base_uri' => 'https://apiv2.pushshift.io/',
            ]);

            try {
                $response = $pushshift->get('reddit/submission/search', [
                    'query' => [
                        'ids' => $this->argument('thread'),
                    ]
                ]);
            } catch (Exception $e) {
                Log::error($e->getMessage(), [
                    'line' => __LINE__,
                    'file' => __FILE__,
                ]);
                exit;
            }

            $threads = json_decode((string) $response->getBody())->data;

//            Cache::put('subreddit:' . $this->argument('subreddit'), $threads, 5);
//        }

        $bar = $this->output->createProgressBar(count($threads));

        foreach ($threads as $pushshiftThread) {
            ProcessThread::dispatch($pushshiftThread)->onQueue('threads');

            $bar->advance();
        }

        $bar->finish();

        $this->info("\n");
    }
}