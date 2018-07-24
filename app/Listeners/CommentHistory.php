<?php

namespace App\Listeners;

use App\Events\CommentSaving;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentHistory
{

    /**
     * Handle the event.
     *
     * @param  CommentUpdating  $event
     * @return void
     */
    public function handle(CommentSaving $event)
    {
        //  do nothing if we have nothing
        if (empty($event->comment->score)) {
            return;
        }

        $event->comment->history()->create(array_except($event->comment->toArray(), ['id']));
    }
}