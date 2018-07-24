<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use GuzzleHttp\Client;
use Log;
use Carbon\Carbon;
use App\User;
use App\Comment;
use Cache;
use App\Author;

class ProcessAuthor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $author;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($author)
    {
        $this->author = $author;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->author == '[deleted]' || $this->author == '%5Bdeleted%5D') {
            return;
        }

        if (str_contains($this->author, 'deleted')) {
            Log::info($this->author);
            return;
        }

        $author = Author::firstOrCreate(['username' => $this->author]);

        //  update no faster than every five minutes
        if ($author->updated_at->lt('15 minutes ago')) {
            return;
        }

        $user = User::where('username', 'loki_racer')->first();
        $client = new Client([
            'base_uri' => 'https://oauth.reddit.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $user->token->access_token,
                'User-Agent'    => 'laravel:' . config('app.name') . ':0.1, (by /u/loki_racer)',
            ],
        ]);

        if (!($redditUser = Cache::get('user:' . $this->author))) {
            try {
                $response = $client->get('/user/' . $this->author . '/about');
            } catch (Exception $e) {
                Log::error($e->getMessage(), [
                    'line' => __LINE__,
                    'file' => __FILE__,
                ]);
                $this->error($e->getMessage());
                exit;
            }

            $results = json_decode((string) $response->getBody());

            if (!isset($results->data)) {
                dd('thread does not exist');
            }

            $redditUser = $results->data;

            Cache::put('user:' . $this->author, $redditUser, 5);
        }

        $author->created_at = Carbon::createFromTimestamp($redditUser->created_utc, 'UTC')->timezone(config('app.timezone'))->toDatetimeString();
        $author->id = $redditUser->id;
        $author->hide_from_robots = $redditUser->hide_from_robots;
        $author->link_karma = $redditUser->link_karma;
        $author->comment_karma = $redditUser->comment_karma;
        $author->is_gold = $redditUser->is_gold;
        $author->is_mod = $redditUser->is_mod;
        $author->verified = $redditUser->verified;
        $author->has_verified_email = $redditUser->has_verified_email;
        $author->raw = $redditUser;

        if (!$author->save()) {
            dd('failed');
        }


        $pushshift = new Client([
            'base_uri' => 'https://apiv2.pushshift.io/',
        ]);
        try {
            $response = $pushshift->get('reddit/comment/search', [
                'query' => [
                    'author' => $author->username,
                ]
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [
                'line' => __LINE__,
                'file' => __FILE__,
            ]);
            exit;
        }

        $rows = collect(json_decode((string) $response->getBody())->data);

        foreach ($rows as $row) {
            $threadId = explode('_', $row->link_id)[1];

            $expanded = Cache::remember('comment_ids_expanded:' . $threadId, 5, function () use ($threadId) {
                $pushshift = new \GuzzleHttp\Client([
                    'base_uri' => 'https://apiv2.pushshift.io/',
                ]);

                try {
                    $response = $pushshift->get('reddit/submission/comment_ids_expanded/' . $threadId);
                } catch (Exception $e) {
                    Log::error($e->getMessage(), [
                        'line' => __LINE__,
                        'file' => __FILE__,
                    ]);
                }

                return collect(json_decode((string) $response->getBody())->data)
                    ->map(function ($row) use ($threadId) {
                        $out = (object) array_combine(['id', 'parent_id', 'created_utc'], explode(',', $row));

                        if ($out->parent_id == 'None') {
                            $out->parent_id = $threadId;
                        }

                        $out->created_at = Carbon::createFromTimestamp($out->created_utc, 'UTC')->timezone(config('app.timezone'));

                        return $out;
                    })
                    ->depth();
            });

            $createdAt = Carbon::createFromTimestamp($row->created_utc, 'UTC')->timezone(config('app.timezone'));

            $current = $expanded->where('id', $row->id)->first();
            $parent = $expanded->where('id', $row->parent_id)->first();

            $comment = Comment::firstOrCreate(
                [
                    'id' => $row->id
                ],[
                    'parent_id'          => $current->parent_id,
                    'subreddit'          => $row->subreddit,
                    'name'               => 't1_' . $row->id,
                    'created_at'         => $current->created_at->toDateTimeString(),
                    'seconds_to_respond' => $parent ? $current->created_at->diffInSeconds($parent->created_at) : null,
                    'depth'              => $current->depth,
                ]
            );
        }

        try {
            $response = $pushshift->get('reddit/submission/search', [
                'query' => [
                    'author' => $author->username,
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

        foreach ($threads as $thread) {
            Comment::firstOrCreate(
                [
                    'id' => $thread->id
                ],[
                    'subreddit'  => $thread->subreddit,
                    'name'       => 't3_' . $thread->id,
                    'created_at' => Carbon::createFromTimestamp($thread->created_utc, 'UTC')->timezone(config('app.timezone')),
                    'depth'      => 0,
                ]
            );
        }
    }
}