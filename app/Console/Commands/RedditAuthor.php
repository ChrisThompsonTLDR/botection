<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessAuthor;

class RedditAuthor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reddit:author {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync the reddit user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->argument('username') == '[deleted]' || $this->argument('username') == '%5Bdeleted%5D') {
            return;
        }

        ProcessAuthor::dispatch($this->argument('username'))->onQueue('authors');
    }
}
