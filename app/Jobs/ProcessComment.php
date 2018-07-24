<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Comment;
use Carbon\Carbon;
use App\Jobs\ProcessAuthor;

class ProcessComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $comment;
    protected $updatedAt;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment   = $comment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $comment = Comment::find($this->comment->id);

        //  check that this data isn't already old
        if (!$comment || ($comment->updated_at->gt('5 minutes ago') && $comment->score)) {
            return;
        }

        $data = array_only((array) $this->comment, [
            'id',
            'subreddit_id',
            'subreddit',
            'score',
            'ups',
            'downs',
            'upvote_ratio',
            'author',
            'name',
            'title',
            'url',
            'spam',
            'stickied',
            'removed',
            'approved',
            'locked',
            'num_reports',
            'quarantine',
            'num_comments',
            'approved_by',
            'ignore_reports',
            'view_count',
            'wls',
            'pwls',
            'num_reports',
            'controversiality',
        ]);

        foreach (['created_utc' => 'created_at', 'edited' => 'edited_at', 'approved_at_utc' => 'approved_at'] as $theirs => $ours) {
            if (!empty($this->comment->{$theirs})) {
                $data[$ours] = Carbon::createFromTimestamp($this->comment->{$theirs}, 'UTC')->timezone(config('app.timezone'));
            }
        }

        $data['body'] = ((!empty($this->comment->selftext)) ? html_entity_decode($this->comment->selftext) : ((!empty($this->comment->body)) ? html_entity_decode($this->comment->body) : null));

        $data['title'] = isset($data['title']) ? html_entity_decode($data['title']) : null;

        $comment->fill($data)->save();

        //  don't do anything outside our subs
        if (in_array($comment->subreddit, config('app.subreddits'))) {
            ProcessAuthor::dispatch($comment->author)->onQueue('authors');
        }
    }
}