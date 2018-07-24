<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Author;
use Carbon\Carbon;
use App\Jobs\ProcessAuthor;

class AuthorResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'author:response';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calculate response time';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $authors = Author::where('updated_at', '>=', Carbon::parse('24 hours ago'))
            ->orderBy('updated_at', 'desc')
            ->with('comments')
            ->chunk(200, function ($authors) {
                $bar = $this->output->createProgressBar($authors->count());

                foreach ($authors as $author) {
                    if ($author->comments->count() > 0) {
                        Author::where('id', $author->id)->update(['seconds_to_respond' => $author->comments->avg('seconds_to_respond')]);
                    } else {                
                        ProcessAuthor::dispatch($author->username)->onQueue('authors');
                    }

                    $bar->advance();
                }

                $bar->finish();

                $this->info("\n");
            });
    }
}
