<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Comment;

class TreeRebuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tree:rebuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'rebuild the tree';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Comment::rebuild();
    }
}
