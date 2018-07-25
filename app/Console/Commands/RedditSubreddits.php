<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Log;
use App\Comment;
use App\User;
use Cache;

use App\Jobs\ProcessThread;

class RedditSubreddits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reddit:subreddits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync the reddit sub';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (config('app.subreddits') as $sub) {
            /*$pushshift = new Client([
                'base_uri' => 'https://apiv2.pushshift.io/',
            ]);

            $response = null;

            try {
                $response = $pushshift->get('reddit/submission/search', [
                    'query' => [
                        'subreddit' => $sub,
                    ]
                ]);
            }
            catch (\GuzzleHttp\Exception\ClientException $e) {
                Log::error($e->getMessage(), [
                    'line' => __LINE__,
                    'file' => __FILE__,
                ]);
                $this->error("\n" . $e->getMessage() . "\n");
                continue;
            }
            catch (Exception $e) {
                Log::error($e->getMessage(), [
                    'line' => __LINE__,
                    'file' => __FILE__,
                ]);
                $this->error("\n" . $e->getMessage() . "\n");
                continue;
            }

            if ($response) {
                $threads = json_decode((string) $response->getBody())->data;

                $this->info("\npushshift threads\n");

                $bar = $this->output->createProgressBar(count($threads));

                foreach ($threads as $pushshiftThread) {
                    ProcessThread::dispatch($pushshiftThread)->onQueue('threads');

                    $bar->advance();
                }

                $bar->finish();
            }*/

            $response = null;

//            $user = User::where('username', 'loki_racer')->first();
//            $client = new Client([
//                'base_uri' => 'https://oauth.reddit.com/',
//                'headers' => [
//                    'Authorization' => 'Bearer ' . $user->token->access_token,
//                    'User-Agent'    => 'laravel:' . config('app.name') . ':0.1, (by /u/loki_racer)',
//                ],
//            ]);


            $client = new Client([
                'base_uri' => 'https://www.reddit.com/',
                'headers' => [
                    'User-Agent' => 'laravel:' . config('app.name') . ':0.1, (by /u/loki_racer)',
                ],
            ]);

            try {
                $response = $client->get('/r/joerogan/new.json');
            }
            catch (\GuzzleHttp\Exception\ClientException $e) {
                Log::error($e->getMessage(), [
                    'line' => __LINE__,
                    'file' => __FILE__,
                ]);
                $this->error("\n" . $e->getMessage() . "\n");
                continue;
            }
            catch (Exception $e) {
                Log::error($e->getMessage(), [
                    'line' => __LINE__,
                    'file' => __FILE__,
                ]);
                $this->error("\n" . $e->getMessage() . "\n");
                continue;
            }

            if ($response) {
                $threads = collect(json_decode((string) $response->getBody())->data->children)->pluck('data');

                $this->info("\nreddit threads\n");

                $bar = $this->output->createProgressBar(count($threads));

                foreach ($threads as $redditThread) {
                    ProcessThread::dispatch($redditThread)->onQueue('threads');

                    $bar->advance();
                }

                $bar->finish();

                $this->info("\n");
            }
            
            sleep(2);
        }
    }
}