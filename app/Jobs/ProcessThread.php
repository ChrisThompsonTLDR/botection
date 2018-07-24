<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use GuzzleHttp\Client;
use Log;
use App\Comment;
use Carbon\Carbon;
use App\Jobs\ProcessComment;
use Cache;

class ProcessThread implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $thread;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($thread)
    {
        $this->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $thread = Comment::firstOrCreate(
            [
                'id' => $this->thread->id
            ],[
                'id'         => $this->thread->id,
                'subreddit'  => $this->thread->subreddit,
                'name'       => 't3_' . $this->thread->id,
                'created_at' => Carbon::createFromTimestamp($this->thread->created_utc, 'UTC')->timezone(config('app.timezone'))->toDateTimeString(),
                'depth'      => 0,
            ]
        );

        $threadId = $this->thread->id;

        $previous = Cache::get('comment_ids_expanded:' . $threadId);

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


        //  do nothing, nothing has changed with thread
//        if ($expanded == $previous) {
//            return;
//        }


        foreach ($expanded->where('parent_id', $threadId) as $row) {
            $this->_children($row, $thread, $expanded);
        }
    }

    private function _children($row, $parent, $rows)
    {
        $createdAt = Carbon::createFromTimestamp($row->created_utc, 'UTC')->timezone(config('app.timezone'));

        $comment = Comment::firstOrCreate(
            [
                'id' => $row->id
            ],[
                'parent_id'          => $parent->id,
                'subreddit'          => $parent->subreddit,
                'name'               => 't1_' . $row->id,
                'created_at'         => $createdAt->toDateTimeString(),
                'seconds_to_respond' => $createdAt->diffInSeconds($parent->created_at),
                'depth'              => $row->depth,
            ]
        );

        foreach ($rows->where('parent_id', $row->id) as $row) {
            $this->_children($row, $comment, $rows);
        }
    }
}