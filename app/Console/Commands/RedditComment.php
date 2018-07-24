<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Log;
use App\Comment;
use Carbon\Carbon;
use App\User;
use App\Jobs\ProcessComment;

class RedditComment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reddit:comment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync comments';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::where('username', 'loki_racer')->first();
        $client = new Client([
            'base_uri' => 'https://oauth.reddit.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $user->token->access_token,
                'User-Agent'    => 'laravel:' . config('app.name') . ':0.1, (by /u/loki_racer)',
            ],
        ]);

        $comments = Comment::whereNotIn('subreddit', config('app.ignoreSubs'))
            ->where('failed_to_find', null)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('created_at', '>=', Carbon::parse('10 hour ago')->toDatetimeString())
                        ->where('updated_at', '<', Carbon::parse('5 minutes ago')->toDatetimeString());
                })
                ->orWhere('score', null);
            })
            ->pluck('subreddit', 'name');

        if ($comments->count() == 0) {
            $this->info("\nnothing to do\n");
            return;
        }

        $grouped = $comments->mapToGroups(function ($sub, $name) {
            return [$sub => $name];
        });

        foreach ($grouped as $sub => $ids) {
            if ($ids->count() == 0) {
                continue;
            }

            $this->info("\n" . $sub . " has " . $ids->count() . " comments to update\n");

            foreach ($ids->chunk(100) as $chunk) {
                try {
                    $response = $client->get('/r/' . $sub . '/api/info/', [
                        'query' => [
                            'id' => $chunk->implode(','),
                        ],
                    ]);
                }
                catch (\GuzzleHttp\Exception\ClientException $e) {
                    Log::error($e->getMessage(), [
                        'line' => __LINE__,
                        'file' => __FILE__,
                    ]);
                    $this->error($e->getMessage());
                    continue;
                }
                catch (Exception $e) {
                    Log::error($e->getMessage(), [
                        'line' => __LINE__,
                        'file' => __FILE__,
                    ]);
                    $this->error($e->getMessage());
                    continue;
                }

                $results = json_decode((string) $response->getBody());

                $processThese = collect($results->data->children)->pluck('data');

                $bar = $this->output->createProgressBar($processThese->count());

                foreach ($processThese as $comment) {
                    ProcessComment::dispatch($comment)->onQueue('comments');

                    $bar->advance();
                }

                //  anything not included in the response, don't ask for again
                foreach ($chunk->diff($processThese->pluck('name')) as $id) {
                    Comment::where('id', explode('_', $id)[1])->update(['failed_to_find' => Carbon::now()->toDatetimeString()]);
                }

                $bar->finish();

                $this->info("\n");
            }
        }
    }
}